<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

class AddStopViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public string $lastOneCountryName,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::AddStop;
    }
}
