<?php

namespace App\DTO\TelegramMessage;

final class AnswerCallbackQueryContext implements TelegramMessageInterface
{
    public function __construct(
        public string $callbackQueryId,
        public ?string $text = null,
        public bool $showAlert = false,
    ) {}
}
