<?php

namespace App\DTO\Internal\TripStopGenerationFinishedViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class AddStopViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $lastOneCountryName,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::AddStop;
    }
}
