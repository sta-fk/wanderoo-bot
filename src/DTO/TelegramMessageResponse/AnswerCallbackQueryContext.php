<?php

namespace App\DTO\TelegramMessageResponse;

final class AnswerCallbackQueryContext implements TelegramMessageInterface
{
    public function __construct(
        public string $callbackQueryId,
        public ?string $text = null,
        public bool $showAlert = false,
    ) {
    }
}
