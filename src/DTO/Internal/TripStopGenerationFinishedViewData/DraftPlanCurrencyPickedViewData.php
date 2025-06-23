<?php

namespace App\DTO\Internal\TripStopGenerationFinishedViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class DraftPlanCurrencyPickedViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $toAmount,
        public string $toCurrency,
        public string $fromAmount,
        public string $fromCurrency,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::ExchangePicked;
    }
}
