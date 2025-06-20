<?php

namespace App\DTO\Internal;

use App\DTO\Context\PlanContext;
use App\Enum\MessageView;

class ViewCurrentDraftPlanViewData implements ViewDataInterface
{
    public function __construct(
        public string $chatId,
        public PlanContext $planContext,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::ViewCurrentDraftPlan;
    }
}
