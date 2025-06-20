<?php

namespace App\DTO\Internal;

use App\Enum\States;
use App\Enum\MessageView;

readonly class StartNewViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::StartNew;
    }
}
