<?php

namespace App\DTO\Internal\InitialStopFlowViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class TripStylePickedViewData implements ViewDataInterface
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
