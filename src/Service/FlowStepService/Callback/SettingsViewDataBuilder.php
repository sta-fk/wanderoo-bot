<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\AddStopViewData;
use App\DTO\Internal\SettingsViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\UserStateStorage;

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
