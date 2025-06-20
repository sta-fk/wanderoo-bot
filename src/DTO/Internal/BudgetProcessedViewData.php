<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\States;
use App\Enum\MessageView;

readonly class BudgetProcessedViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public float $budget,
        public string $currency,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::BudgetProcessed;
    }
}
