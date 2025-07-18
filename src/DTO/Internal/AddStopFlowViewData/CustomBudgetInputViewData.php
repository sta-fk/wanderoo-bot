<?php

namespace App\DTO\Internal\AddStopFlowViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class CustomBudgetInputViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $currency,
        public float $potentialAmount,
        public bool $validationPassed = true,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CustomBudgetInput;
    }
}
