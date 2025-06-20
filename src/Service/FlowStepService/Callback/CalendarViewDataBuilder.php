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
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Calendar->value);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        [$year, $month] = explode('_', substr($update->callbackQuery->data, strlen(CallbackQueryData::Calendar->value)));

        return ViewDataCollection::createWithSingleViewData(
            new CalendarViewData(
                $update->callbackQuery->message->chat->id,
                $update->callbackQuery->message->messageId,
                $year,
                $month
            )
        );
    }
}
