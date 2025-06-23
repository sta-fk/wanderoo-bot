<?php

namespace App\DTO\Internal\InitialStopFlowViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class CalendarViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public int $messageId,
        public int $year,
        public int $month,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::Calendar;
    }
}
