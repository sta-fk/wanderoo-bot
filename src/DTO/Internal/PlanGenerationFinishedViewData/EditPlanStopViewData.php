<?php

namespace App\DTO\Internal\PlanGenerationFinishedViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class EditPlanStopViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $countryName,
        public string $cityName,
        public int $stopIndex,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::EditPlanStop;
    }
}
