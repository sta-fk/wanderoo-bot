<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Context\PlanContext;
use App\DTO\Internal\InterestsViewData;
use App\DTO\Internal\ReuseOrNewInterestsViewData;
use App\DTO\Internal\TripStylePickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class TripStyleViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::TripStyle->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForTripStyle];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $tripStyle = substr($update->callbackQuery->data, strlen(CallbackQueryData::TripStyle->value));
        $context->currentStopDraft->tripStyle = $tripStyle;

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
            ->setNextState($nextState)
        ;

        return $viewDataCollection;
    }

    private function buildReuseOrNewInterestsViewData(int $chatId, PlanContext $context): array
    {
        $previousInterests = null !== $context->stops[count($context->stops) - 1]
            ? $context->stops[count($context->stops) - 1]->getInterestsLabels()
            : [];

        $nextViewData = new ReuseOrNewInterestsViewData($chatId, $previousInterests);

        return [$nextViewData, States::WaitingForReuseOrNewInterests];
    }

    private function buildInterestsViewData(int $chatId, PlanContext $context): array
    {
        $nextViewData = new InterestsViewData(chatId: $chatId, cityName: $context->currentStopDraft->cityName);

        return [$nextViewData, States::WaitingForInterests];
    }
}
