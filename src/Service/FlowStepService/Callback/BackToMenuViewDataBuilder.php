<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\MenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Service\FlowStepService\ViewDataBuilderInterface;

readonly class BackToMenuViewDataBuilder implements ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::BackToMenu->value === $update->callbackQuery->data;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createWithSingleViewData(
            new MenuViewData($update->message->chat->id)
        );
    }
}
