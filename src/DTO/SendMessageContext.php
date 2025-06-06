<?php

namespace App\DTO;

use App\DTO\Request\TelegramUpdate;
use App\Enum\States;

class SendMessageContext
{
    public function __construct(
        public int $chatId,
        public string $text,
        public ?array $replyMarkup = null,
        public ?States $nextState = null,
    ) {
    }
}
