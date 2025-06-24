<?php

namespace App\DTO\Internal\PlanGenerationFinishedViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class EditPlanContextEntryPointViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public array $stops,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::EditPlanContextEntry;
    }
}
