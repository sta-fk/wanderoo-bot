<?php

namespace App\Service\FlowStepService\Commands;

use App\DTO\Internal\ViewCurrentDraftPlanViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\TelegramCommands;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class ViewCurrentDraftPlanViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return TelegramCommands::ViewCurrentDraftPlan->value === $update->message?->text;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        return ViewDataCollection::createWithSingleViewData(new ViewCurrentDraftPlanViewData($chatId, $context));
    }
}
