<?php

namespace App\DTO\TelegramMessageResponse;

final class SendDocumentContext implements TelegramMessageInterface
{
    public function __construct(
        public int $chatId,
        public string $documentUrl,
        public ?string $caption = null,
        public ?array $replyMarkup = null,
    ) {
    }
}
