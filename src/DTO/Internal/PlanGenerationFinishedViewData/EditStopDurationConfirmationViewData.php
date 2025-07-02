<?php

namespace App\DTO\Internal\PlanGenerationFinishedViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

class EditStopDurationConfirmationViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $message
    ) {}

    public function getCurrentView(): MessageView
    {
        return MessageView::EditStopDurationConfirmation;
    }
}
