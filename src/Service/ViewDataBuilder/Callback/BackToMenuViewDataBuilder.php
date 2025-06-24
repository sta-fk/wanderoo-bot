<?php

namespace App\Service\ViewDataBuilder\Callback;

use App\DTO\Internal\MenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;

readonly class BackToMenuViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::BackToMenu);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createWithSingleViewData(new MenuViewData($update->getChatId()));
    }
}
