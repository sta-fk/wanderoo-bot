<?php

namespace App\DTO\Internal\MenuActionsViewData\SettingsActionsViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class DefaultCurrencyMenuViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::DefaultCurrency;
    }
}
