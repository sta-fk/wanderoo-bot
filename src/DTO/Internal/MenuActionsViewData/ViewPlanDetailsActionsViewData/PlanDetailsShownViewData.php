<?php

namespace App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class PlanDetailsShownViewData implements ViewDataInterface
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
