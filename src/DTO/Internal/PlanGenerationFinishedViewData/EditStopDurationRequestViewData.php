<?php

namespace App\DTO\Internal\PlanGenerationFinishedViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

class EditStopDurationRequestViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $cityName
    ) {}

    public function getCurrentView(): MessageView
    {
        return MessageView::EditStopDurationRequest;
    }
}
