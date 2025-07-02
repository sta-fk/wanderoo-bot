<?php

namespace App\Service\ViewDataBuilder\Callback\DraftStopFinishedActions;

use App\DTO\Internal\TripStopGenerationFinishedViewData\PlanGenerationFinishedViewData;
use App\DTO\Internal\TripStopGenerationFinishedViewData\PlanIsGeneratingViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\TripPlanner\PlanBuilderService;
use App\Service\TripPlanner\TripPlanFormatterInterface;
use App\Service\UserStateStorage;

readonly class GeneratingTripPlanViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlanBuilderService $planBuilderService,
        private TripPlanFormatterInterface $tripPlanFormatter,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::GeneratingTripPlan);
    }

    public function supportsStates(): array
    {
        return [States::TripStopCreationFinished];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
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

        $viewDataCollection
            ->add(new PlanGenerationFinishedViewData($chatId))
            ->setNextState(States::PlanGenerationFinished);

        return $viewDataCollection;
    }
}
