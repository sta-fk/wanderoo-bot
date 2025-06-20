<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class CountryPickedViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public int $callbackQueryId,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CountryPicked;
    }
}
