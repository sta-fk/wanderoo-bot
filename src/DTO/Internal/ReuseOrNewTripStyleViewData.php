<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class ReuseOrNewTripStyleViewData implements ViewDataInterface
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
