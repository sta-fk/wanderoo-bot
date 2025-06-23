<?php

namespace App\DTO\Internal\MenuActionsViewData;

use App\DTO\Internal\ViewDataInterface;
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
