<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class ExchangePickedViewData implements ViewDataInterface
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
