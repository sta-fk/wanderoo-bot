<?php

namespace App\DTO\Internal\InitialStopFlowViewData;

use App\DTO\Internal\ViewDataInterface;
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
