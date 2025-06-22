<?php

namespace App\Service\FlowViewer\Callback\InitialStopFlowActions;

use App\DTO\Internal\CustomDurationInputViewData;
use App\DTO\Internal\DurationProcessedViewData;
use App\DTO\Internal\StartDateViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowViewer\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class DurationViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::Duration);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForDurationPicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $durationValue = $update->getCustomCallbackQueryData(CallbackQueryData::Duration);
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        if (CallbackQueryData::Custom->value === $durationValue) {
            return ViewDataCollection::createStateAwareWithSingleViewData(
                new CustomDurationInputViewData($chatId),
                States::WaitingForCustomDurationInput,
            );
        }

        $context->currentStopDraft->duration = (int) $durationValue;
        $this->userStateStorage->saveContext($chatId, $context);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add(new DurationProcessedViewData($chatId, $context->currentStopDraft->duration))
            ->add(new StartDateViewData($chatId))
            ->setNextState(States::WaitingForStartDate);

        return $viewDataCollection;
    }
}
