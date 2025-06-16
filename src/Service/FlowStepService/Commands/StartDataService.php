<?php

namespace App\Service\FlowStepService\Commands;

use App\DTO\Internal\StartViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\Request\TelegramUpdate;
use App\DTO\TelegramResponseMessage\TelegramMessageCollection;
use App\DTO\TelegramResponseMessage\TelegramViewDataCollection;
use App\Enum\TelegramCommands;
use App\Service\FlowStepService\FlowViewDataServiceInterface;

readonly class StartDataService implements FlowViewDataServiceInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return TelegramCommands::Start->value === $update->message?->text;
    }

    public function buildNextStepViewData(TelegramUpdate $update): ViewDataInterface
    {
        return new StartViewData($update->message->chat->id);
    }
}
