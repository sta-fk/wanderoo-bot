<?php

namespace App\DTO\Internal\InitialStopFlowViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class DurationProcessedViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public int $currentStopDuration,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::DurationProcessed;
    }
}
