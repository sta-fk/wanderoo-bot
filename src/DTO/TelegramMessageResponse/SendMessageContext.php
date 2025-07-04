<?php

namespace App\DTO\TelegramMessageResponse;

final class SendMessageContext implements TelegramMessageInterface
{
    public function __construct(
        public int $chatId,
        public string $text,
        public ?array $replyMarkup = null,
    ) {
    }
}
