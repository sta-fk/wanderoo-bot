<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class UniversalDeletePreviousMessageViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public int $messageIdToDelete
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::UniversalDeletePreviousMessage;
    }
}
