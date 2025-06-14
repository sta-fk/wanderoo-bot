<?php

namespace App\DTO\Request;

class TelegramCallbackQuery
{
    public string $id;
    public string $data;
    public TelegramMessage $message;
    public TelegramFrom $from;
}
