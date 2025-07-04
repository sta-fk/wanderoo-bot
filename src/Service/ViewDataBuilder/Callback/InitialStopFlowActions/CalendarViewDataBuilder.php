<?php

namespace App\Service\ViewDataBuilder\Callback\InitialStopFlowActions;

use App\DTO\Internal\InitialStopFlowViewData\CalendarViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;

readonly class CalendarViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::Calendar);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStartDate];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        [$year, $month] = explode('_', $update->getCustomCallbackQueryData(CallbackQueryData::Calendar));

        return ViewDataCollection::createWithSingleViewData(
            new CalendarViewData(
                $update->getChatId(),
                $update->getCallbackMessageId(),
                $year,
                $month
            )
        );
    }
}
