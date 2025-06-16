<?php

namespace App\DTO\Internal\ViewData;

use App\Enum\States;
use App\Enum\View;

readonly class StartViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
    ) {}

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function getCurrentView(): View
    {
        return View::Start;
    }

    public function getState(): States
    {
        return States::WaitingForStart;
    }
}
