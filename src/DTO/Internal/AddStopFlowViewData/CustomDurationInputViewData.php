<?php

namespace App\DTO\Internal\AddStopFlowViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class CustomDurationInputViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $validationFailedMessage = '',
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CustomDurationInput;
    }
}
