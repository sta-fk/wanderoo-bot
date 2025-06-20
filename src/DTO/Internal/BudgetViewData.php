<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class BudgetViewData implements ViewDataInterface
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
