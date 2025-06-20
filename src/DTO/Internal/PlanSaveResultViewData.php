<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

class PlanSaveResultViewData implements ViewDataInterface
{
    public function __construct(
        public int $callbackQueryId,
        public ?string $tripTitle = null,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::PlanSaveResult;
    }
}
