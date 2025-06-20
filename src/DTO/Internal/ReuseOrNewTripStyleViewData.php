<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;
use App\Enum\States;

class ReuseOrNewTripStyleViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $lastOneTripStyle,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::ReuseOrNewTripStyle;
    }
}
