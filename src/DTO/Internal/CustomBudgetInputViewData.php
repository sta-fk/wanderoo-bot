<?php

namespace App\DTO\Internal;

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
