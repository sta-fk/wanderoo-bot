<?php

namespace App\Service\ViewDataBuilder\Callback\MenuActions;

use App\DTO\Internal\MenuActionsViewData\SettingsViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;

readonly class SettingsViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::Settings);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createWithSingleViewData(
            new SettingsViewData($update->getChatId()),
        );
    }
}
