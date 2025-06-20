<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;
use App\Enum\States;

class BudgetViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $currency,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::Budget;
    }
}
