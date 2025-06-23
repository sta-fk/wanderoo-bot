<?php

namespace App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

class ViewedPlanCurrencyChangedViewData implements ViewDataInterface
{
    public function __construct(
        public int $callbackQueryId,
        public string $currency,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::ViewedPlanCurrencyChanged;
    }
}
