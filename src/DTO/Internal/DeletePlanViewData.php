<?php

namespace App\DTO\Internal;

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
