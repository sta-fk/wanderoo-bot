<?php

namespace App\DTO\Internal\TripStopGenerationFinishedViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class DraftPlanCurrencyCountryInputViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::ExchangeCountryInput;
    }
}
