<?php

namespace App\DTO;

class SendMessageContext
{
    public function __construct(
        public int $chatId,
        public string $text,
        public ?array $replyMarkup = null,
    ) {
    }
}
