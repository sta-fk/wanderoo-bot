<?php

namespace App\Controller;

use App\DTO\Request\TelegramUpdate;
use App\Http\ApiResponseTrait;
use App\Http\BaseApiController;
use App\Service\TelegramService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TelegramController extends BaseApiController
{
    use ApiResponseTrait;

    #[Route('/webhook', name: 'telegram_webhook', methods: ['POST'])]
    public function webhook(Request $request, TelegramService $telegramService): JsonResponse
    {
        /** @var TelegramUpdate $update */
        try {
            $update = $this->serializer->deserialize($request->getContent(), TelegramUpdate::class, 'json');
        } catch (\Throwable) {
            return $this->error('Invalid payload');
        }

        $telegramService->handleUpdate($update);

        return $this->success();
    }
}
