<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\ExchangeChoiceViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\ViewDataBuilderInterface;

readonly class ExchangerViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::Exchanger);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createStateAwareWithSingleViewData(
            new ExchangeChoiceViewData($update->getChatId()),
            States::WaitingForExchangeChoicePicked
        );
    }
}
