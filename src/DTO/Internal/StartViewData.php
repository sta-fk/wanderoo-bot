<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\States;
use App\Enum\MessageView;

readonly class StartViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
    ) {}

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::Start;
    }

    public function getNextStates(): States
    {
        return States::WaitingForStartNew;
    }
}
