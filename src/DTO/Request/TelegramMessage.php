<?php

namespace App\DTO\Request;

class TelegramMessage
{
    public int $messageId;
    public TelegramFrom $from;
    public TelegramChat $chat;
    public ?string $text = null;
}
