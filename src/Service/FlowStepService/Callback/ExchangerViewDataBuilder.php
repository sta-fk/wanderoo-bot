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
        return null !== $update->callbackQuery
            && CallbackQueryData::Exchanger->value === $update->callbackQuery->data;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createStateAwareWithSingleViewData(
            new ExchangeChoiceViewData($update->callbackQuery->message->chat->id),
            States::WaitingForExchangeChoicePicked
        );
    }
}
