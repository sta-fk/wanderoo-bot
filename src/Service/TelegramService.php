<?php

namespace App\Service;

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
        $this->apiUrl = "https://api.telegram.org/bot{$params->get('telegram_bot_token')}/";
        $this->client = $client;
    }

    public function handleMessage(int $chatId, string $text): void
    {
        $reply = match (true) {
            str_starts_with($text, '/start') => 'Привіт! Я твій тревел-планувальник 🚀',
            str_starts_with($text, '/help') => 'Напиши /new_trip щоб створити нову подорож.',
            default => 'Команда не розпізнана. Напиши /help.',
        };

        $this->sendMessage($chatId, $reply);
    }

    public function sendMessage(int $chatId, string $text): void
    {
        $this->client->request('POST', $this->apiUrl . 'sendMessage', [
            'json' => [
                'chat_id' => $chatId,
                'text' => $text,
            ],
        ]);
    }
}
