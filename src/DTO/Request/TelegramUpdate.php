<?php

namespace App\DTO\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

class TelegramUpdate
{
    public ?TelegramMessage $message = null;

    #[SerializedName("callback_query")]
    public ?TelegramCallbackQuery $callbackQuery = null;
}
