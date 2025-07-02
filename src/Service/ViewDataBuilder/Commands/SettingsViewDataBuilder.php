<?php

namespace App\Service\ViewDataBuilder\Commands;

use App\DTO\Internal\MenuActionsViewData\SettingsViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\TelegramCommands;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;

readonly class SettingsViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsMessageUpdate(TelegramCommands::Settings);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createWithSingleViewData(
            new SettingsViewData($update->getChatId()),
        );
    }
}
