<?php

namespace App\Service\ViewDataBuilder\Callback\PlanGenerationFinished\EditContextActions;

use App\DTO\Internal\PlanGenerationFinishedViewData\EditStopDurationRequestViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;

readonly class EditStopDurationRequestViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $stateStorage,
    ) {}

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::EditStopDuration);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->stateStorage->getContext($chatId);

        $stopIndex = (int) $update->getCustomCallbackQueryData(CallbackQueryData::EditStopDuration);
        $context->editingStopIndex = $stopIndex;
        $this->stateStorage->saveContext($chatId, $context);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new EditStopDurationRequestViewData($chatId, $context->stops[$stopIndex]->cityName),
            States::WaitingForEditingDurationInput
        );
    }
}
