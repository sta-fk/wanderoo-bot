<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class DurationProcessedViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public int $duration,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::DurationProcessed;
    }
}
