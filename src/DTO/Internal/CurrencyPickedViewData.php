<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class CurrencyPickedViewData implements ViewDataInterface
{
    public function __construct(
        public int $callbackQueryId,
        public string $currency,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CurrencyPicked;
    }
}
