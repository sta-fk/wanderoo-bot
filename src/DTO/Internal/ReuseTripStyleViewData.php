<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;
use App\Enum\States;

class ReuseTripStyleViewData implements ViewDataInterface
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
