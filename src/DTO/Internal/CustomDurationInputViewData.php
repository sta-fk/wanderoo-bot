<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\States;
use App\Enum\MessageView;

readonly class CustomDurationInputViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public bool $validationPassed = true,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CustomDurationInput;
    }
}
