<?php

namespace App\Controller;

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
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['message'])) {
            return new JsonResponse(['ok' => false, 'reason' => 'Invalid payload'], 400);
        }

        $chatId = $data['message']['chat']['id'] ?? null;
        $text = $data['message']['text'] ?? '';

        if ($chatId) {
            $telegramService->handleMessage($chatId, $text);
        }

        return new JsonResponse(['ok' => true]);
    }
}
