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
        try {
            $update = $this->serializer->deserialize($request->getContent(), TelegramUpdate::class, 'json');

            $telegramService->handleUpdate($update);
        } catch (\Throwable $exception) {
            return $this->error($exception->getMessage());
        }

        return $this->success();
    }
}
