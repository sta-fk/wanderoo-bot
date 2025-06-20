<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

class SavedPlanNotFoundViewData implements ViewDataInterface
{
    public function __construct(
        public int $callbackQueryId,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::SavedPlanNotFound;
    }
}
