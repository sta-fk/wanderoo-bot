<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\DefaultCurrencyMenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepService\ViewDataBuilderInterface;

class DefaultCurrencyMenuViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::DefaultCurrency);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createWithSingleViewData(
            new DefaultCurrencyMenuViewData($update->getChatId())
        );
    }
}
