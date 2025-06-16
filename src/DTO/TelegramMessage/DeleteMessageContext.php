<?php

namespace App\DTO\TelegramMessage;

final class DeleteMessageContext implements TelegramMessageInterface
{
    public function __construct(
        public int $chatId,
        public int $messageId,
    ) {}
}
