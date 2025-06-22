<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CalendarViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepService\ViewDataBuilderInterface;

readonly class CalendarViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::Calendar);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        [$year, $month] = explode('_', $update->getCustomCallbackQueryData(CallbackQueryData::Calendar));

        return ViewDataCollection::createWithSingleViewData(
            new CalendarViewData(
                $update->callbackQuery->id,
                $update->getCallbackMessageId(),
                $year,
                $month
            )
        );
    }
}
