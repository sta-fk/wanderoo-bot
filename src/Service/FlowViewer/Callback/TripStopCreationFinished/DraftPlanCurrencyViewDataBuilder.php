<?php

namespace App\Service\FlowViewer\Callback\TripStopCreationFinished;

use App\DTO\Internal\DraftPlanCurrencyChoiceViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowViewer\ViewDataBuilderInterface;

readonly class DraftPlanCurrencyViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::DraftPlanCurrency);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createStateAwareWithSingleViewData(
            new DraftPlanCurrencyChoiceViewData($update->getChatId()),
            States::WaitingForDraftPlanCurrencyChoicePicked
        );
    }
}
