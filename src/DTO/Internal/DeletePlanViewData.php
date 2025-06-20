<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

class DeletePlanViewData implements ViewDataInterface
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
