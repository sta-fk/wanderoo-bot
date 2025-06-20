<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

class PlanDetailsShownViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $requiredPlanId,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::PlanDetailsShown;
    }
}
