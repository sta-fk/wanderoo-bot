<?php

namespace App\DTO\Internal;

use App\Enum\States;
use App\Enum\MessageView;

readonly class CityPickedViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public int $callbackQueryId,
        public bool $isAddingStopFlow
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CityPicked;
    }
}
