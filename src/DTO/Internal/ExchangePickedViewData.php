<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

class ExchangePickedViewData implements ViewDataInterface
{
    public function __construct(
        public int $callbackQueryId,
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
