<?php

namespace App\Service\ViewDataBuilder\Callback\MenuActions\SettingsActions;

use App\DTO\Internal\MenuActionsViewData\SettingsActionsViewData\DefaultCurrencyMenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;

readonly class DefaultCurrencyMenuViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::DefaultCurrency);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createStateAwareWithSingleViewData(
            new DefaultCurrencyMenuViewData($update->getChatId()),
            States::WaitingForDefaultCurrencyMenuContinue
        );
    }
}
