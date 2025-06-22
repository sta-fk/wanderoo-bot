<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Context\PlanContext;
use App\DTO\Context\StopContext;
use App\DTO\Internal\ReuseOrNewInterestsViewData;
use App\DTO\Internal\ReuseTripStyleViewData;
use App\DTO\Internal\TripStyleViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class ReuseOrNewTripStyleViewDataBuilder implements StateAwareViewDataBuilderInterface
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
        return [States::WaitingForReuseOrNewTripStyle];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $action = $update->getCustomCallbackQueryData(CallbackQueryData::TripStyle);

        if ($action === CallbackQueryData::Reuse->value) {
            $currentStopDraft = $context->currentStopDraft;

            $currentStopDraft->tripStyle = $context->getLastSavedStop()->tripStyle;
            $this->userStateStorage->saveContext($chatId, $context);

            $viewDataCollection = new ViewDataCollection();
            $viewDataCollection->add($this->buildReuseTripStyleViewData($update->callbackQuery->id, $currentStopDraft));
            $viewDataCollection->add($this->buildReuseOrNewInterestsViewData($chatId, $context));
            $viewDataCollection->setNextState(States::WaitingForReuseOrNewInterests);

            return $viewDataCollection;
        }

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new TripStyleViewData($chatId),
            States::WaitingForTripStyle
        );
    }

    private function buildReuseTripStyleViewData(int $callbackQueryId, StopContext $currentStopDraft): ReuseTripStyleViewData
    {
        return new ReuseTripStyleViewData(
            $callbackQueryId,
            $currentStopDraft->cityName,
            $currentStopDraft->getTripStyleLabel()
        );
    }

    private function buildReuseOrNewInterestsViewData(int $chatId, PlanContext $context): ReuseOrNewInterestsViewData
    {
        $previousInterests = null !== $context->getLastSavedStop()
            ? $context->getLastSavedStop()->getInterestsLabels()
            : [];

        return new ReuseOrNewInterestsViewData($chatId, $previousInterests);
    }
}
