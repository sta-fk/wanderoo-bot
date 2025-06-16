<?php

namespace App\Service\FlowStepService;

use App\DTO\Internal\ViewData\StartViewData;
use App\DTO\Internal\ViewData\ViewDataInterface;
use App\DTO\Request\TelegramUpdate;
use App\Enum\TelegramCommands;

readonly class CommandsStartService implements FlowStepServiceInterface
{
    public function supports(TelegramUpdate $update): bool
    {
        return TelegramCommands::Start->value === $update->message?->text;
    }

    public function buildViewData(TelegramUpdate $update): ViewDataInterface
    {
        return new StartViewData($update->message->chat->id);
    }
}
