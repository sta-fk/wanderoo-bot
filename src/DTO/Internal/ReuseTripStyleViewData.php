<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class ReuseTripStyleViewData implements ViewDataInterface
{
    public function __construct(
        public int $callbackQueryId,
        public string $tripStyle,
        public string $cityName
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::ReuseTripStyle;
    }
}
