<?php

namespace App\DTO\Internal\AddStopFlowViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class ReuseOrNewInterestsViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public array $interests,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::ReuseOrNewInterests;
    }
}
