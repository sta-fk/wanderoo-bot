<?php

namespace App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class DeletePlanViewData implements ViewDataInterface
{
    public function __construct(
        public int $callbackQueryId,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::DeletePlan;
    }
}
