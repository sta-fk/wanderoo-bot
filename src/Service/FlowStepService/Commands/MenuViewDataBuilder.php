<?php

namespace App\Service\FlowStepService\Commands;

use App\DTO\Internal\MenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\TelegramCommands;
use App\Service\FlowStepService\ViewDataBuilderInterface;

readonly class MenuViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return TelegramCommands::Start->value === $update->message?->text;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createWithSingleViewData(
            new MenuViewData($update->message->chat->id)
        );
    }
}
