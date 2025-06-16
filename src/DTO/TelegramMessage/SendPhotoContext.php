<?php

namespace App\DTO\TelegramMessage;

final class SendPhotoContext implements TelegramMessageInterface
{
    public function __construct(
        public int $chatId,
        public string $photoUrl,
        public ?string $caption = null,
        public ?array $replyMarkup = null,
    ) {}
}
