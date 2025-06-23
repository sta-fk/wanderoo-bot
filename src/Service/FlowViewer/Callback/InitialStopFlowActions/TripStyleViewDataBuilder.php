<?php

namespace App\Service\FlowViewer\Callback\InitialStopFlowActions;

use App\DTO\Context\PlanContext;
use App\DTO\Internal\InitialStopFlowViewData\InterestsViewData;
use App\DTO\Internal\AddStopFlowViewData\ReuseOrNewInterestsViewData;
use App\DTO\Internal\InitialStopFlowViewData\TripStylePickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowViewer\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class TripStyleViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::TripStyle);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForTripStyle];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $context->currentStopDraft->tripStyle =
            $update->getCustomCallbackQueryData(CallbackQueryData::TripStyle);

        $this->userStateStorage->saveContext($chatId, $context);

        $processedViewData = new TripStylePickedViewData(
            $chatId,
            $update->callbackQuery->id,
            $context->isAddingStopFlow,
            $context->currentStopDraft->getTripStyleLabel()
        );

        [$nextViewData, $nextState] =
            $context->isAddingStopFlow
                ? $this->buildReuseOrNewInterestsViewData($chatId, $context)
                : $this->buildInterestsViewData($chatId, $context);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add($processedViewData)
            ->add($nextViewData)
            ->setNextState($nextState);

        return $viewDataCollection;
    }

    private function buildReuseOrNewInterestsViewData(int $chatId, PlanContext $context): array
    {
        $previousInterests = null !== $context->getLastSavedStop()
            ? $context->getLastSavedStop()->getInterestsLabels()
            : [];

        return [
            new ReuseOrNewInterestsViewData($chatId, $previousInterests),
            States::WaitingForReuseOrNewInterests
        ];
    }

    private function buildInterestsViewData(int $chatId, PlanContext $context): array
    {
        $nextViewData = new InterestsViewData(
            chatId: $chatId,
            cityName: $context->currentStopDraft->cityName
        );

        return [$nextViewData, States::WaitingForInterests];
    }
}
