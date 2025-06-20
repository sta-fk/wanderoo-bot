<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;
use App\Enum\States;

class TripStylePickedViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public int $callbackQueryId,
        public bool $isAddingStopFlow,
        public string $tripStyleLabel,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::TripStylePicked;
    }
}
