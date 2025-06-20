<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

class PlanIsGeneratingViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $tripPlanSplitMessage,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::PlanIsGenerating;
    }
}
