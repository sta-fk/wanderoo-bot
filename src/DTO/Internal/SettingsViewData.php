<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class SettingsViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::Settings;
    }
}
