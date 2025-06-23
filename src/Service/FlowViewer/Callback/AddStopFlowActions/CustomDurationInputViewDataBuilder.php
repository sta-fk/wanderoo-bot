<?php

namespace App\Service\FlowViewer\Callback\AddStopFlowActions;

use App\DTO\Internal\AddStopFlowViewData\CustomDurationInputViewData;
use App\DTO\Internal\InitialStopFlowViewData\DurationProcessedViewData;
use App\DTO\Internal\AddStopFlowViewData\ReuseOrNewTripStyleViewData;
use App\DTO\Internal\InitialStopFlowViewData\StartDateViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\FlowViewer\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class CustomDurationInputViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->isMessageUpdate();
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCustomDurationInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        if (!is_numeric($update->message->text) || $update->message->text < 0 || $update->message->text >= 30) {
            return ViewDataCollection::createWithSingleViewData(
                new CustomDurationInputViewData($chatId, false)
            );
        }

        $context->currentStopDraft->duration = (int)$update->message->text;

        $this->userStateStorage->saveContext($chatId, $context);

        $processedViewData = new DurationProcessedViewData($chatId, $context->currentStopDraft->duration);

        [$nextViewData, $nextState] =
            $context->isAddingStopFlow
                ? [
                    new ReuseOrNewTripStyleViewData($chatId, $context->getLastSavedStop()->getTripStyleLabel()),
                    States::WaitingForReuseOrNewTripStyle
                ]
                : [new StartDateViewData($chatId), States::WaitingForStartDate];

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add($processedViewData)
            ->add($nextViewData)
            ->setNextState($nextState)
        ;

        return $viewDataCollection;
    }
}
