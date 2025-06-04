<?php

namespace App\Service;

use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TelegramService
{
    private string $apiUrl;
    private HttpClientInterface $client;

    public function __construct(
        ParameterBagInterface $params,
        HttpClientInterface $client
    ) {
        $this->apiUrl = sprintf("%s%s", $params->get('telegram_bot_api_url'), $params->get('telegram_bot_token'));
        $this->client = $client;
    }

    public function handleUpdate(TelegramUpdate $update): void
    {
        if ($update->message?->text === States::Start->value) {
            $chatId = $update->message->chat->id;
            $this->sendWelcomeMessage($chatId);
        }

        if ($update->callbackQuery) {
            $chatId = $update->callbackQuery->message->chat->id;
            $data = $update->callbackQuery->data;

            if ($data === 'start_yes') {
                $this->sendMarkdownMessage($chatId, 'Супер! Почнімо ✨');
            }
        }
    }

    public function sendWelcomeMessage(int $chatId): void
    {
        $text = <<<TEXT
Привіт! Я ✈️ Wanderoo — бот, що допоможе спланувати твою мандрівку.

Я поставлю кілька простих запитань і згенерую персональний тревел-план: що подивитись, куди сходити, що скуштувати 🍜

Почнемо?
TEXT;

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🧳 Так, хочу план!', 'callback_data' => 'start_yes'],
                    ['text' => '❌ Ні, просто дивлюсь', 'callback_data' => 'start_no'],
                ]
            ]
        ];

        $this->sendMarkdownMessage($chatId, $text, $keyboard);
    }

    public function sendMarkdownMessage(int $chatId, string $text, ?array $replyMarkup = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        $this->client->request('POST', "{$this->apiUrl}/sendMessage", [
            'json' => $payload
        ]);
    }
}
