<?php

namespace App\DTO\TelegramMessageResponse;

final class EditMessageTextContext implements TelegramMessageInterface
{
    public function __construct(
        public int $chatId,
        public int $messageId,
        public string $text,
        public ?array $replyMarkup = null,
    ) {
    }
}
