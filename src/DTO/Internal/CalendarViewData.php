<?php

namespace App\DTO\Internal;

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
