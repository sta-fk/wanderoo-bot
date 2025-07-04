<?php

namespace App\DTO\Internal\PlanGenerationFinishedViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class PlanSaveResultViewData implements ViewDataInterface
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
