<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class DatePickedViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public int $callbackQueryId,
        public \DateTimeImmutable $startDate,
        public \DateTimeImmutable $endDate,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::DatePicked;
    }
}
