<?php

namespace App\DTO\Request;

class TelegramUpdate
{
    public ?TelegramMessage $message = null;
    public ?TelegramCallbackQuery $callbackQuery = null;
}
