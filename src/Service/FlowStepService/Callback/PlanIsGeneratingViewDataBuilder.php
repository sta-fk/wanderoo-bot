<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\PlanGenerationFinishedViewData;
use App\DTO\Internal\PlanIsGeneratingViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\TripPlanner\PlanBuilderService;
use App\Service\TripPlanner\TripPlanFormatterInterface;
use App\Service\UserStateStorage;

readonly class PlanIsGeneratingViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlanBuilderService $planBuilderService,
        private TripPlanFormatterInterface $tripPlanFormatter,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::GeneratingTripPlan->value === $update->callbackQuery->data;
    }

    public function supportsStates(): array
    {
        return [States::TripStopCreationFinished];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $this->userStateStorage->saveContext($chatId, $context);

        $tripPlan = $this->planBuilderService->buildPlan($context);
        $texts = $this->tripPlanFormatter->splitFormattedPlan($tripPlan);
        $viewDataCollection = new ViewDataCollection();

        $i = 0;
        while ($i < count($texts)) {
            $viewDataCollection->add(new PlanIsGeneratingViewData($chatId, $texts[$i]));
            $i++;
        }

        $viewDataCollection->add(new PlanGenerationFinishedViewData($chatId));
        $viewDataCollection->setNextState(States::PlanGenerationFinished);

        return $viewDataCollection;
    }
}
