<?php

namespace App\Service\FlowStepService\Commands;

use App\DTO\Internal\SettingsViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\TelegramCommands;
use App\Service\FlowStepService\ViewDataBuilderInterface;

readonly class SettingsViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return TelegramCommands::Settings->value === $update->message?->text;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createWithSingleViewData(
            new SettingsViewData($update->message->chat->id),
        );
    }
}
