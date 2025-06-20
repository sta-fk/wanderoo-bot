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
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::TripStyle->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForReuseOrNewTripStyle];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $action = substr($update->callbackQuery->data, strlen(CallbackQueryData::TripStyle->value));

        if ($action === CallbackQueryData::Reuse->value) {
            $lastOneStop = ($context->stops[count($context->stops) - 1]);
            $currentStopDraft = $context->currentStopDraft;

            $currentStopDraft->tripStyle = $lastOneStop->tripStyle;
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
        $previousInterests = null !== $context->stops[count($context->stops) - 1]
            ? $context->stops[count($context->stops) - 1]->getInterestsLabels()
            : [];

        return new ReuseOrNewInterestsViewData($chatId, $previousInterests);
    }
}
