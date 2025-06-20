<?php

namespace App\DTO\Internal;

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
