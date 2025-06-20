<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\PlanSaveResultViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Repository\UserRepository;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\TripPersister;
use App\Service\TripPlanner\PlanBuilderService;
use App\Service\UserStateStorage;

readonly class PlanSaveResultViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private TripPersister $tripPersister,
        private UserRepository $userRepository,
        private UserStateStorage $stateStorage,
        private PlanBuilderService $planBuilderService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::SaveGeneratedPlan->value === $update->callbackQuery->data;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;

        $context = $this->stateStorage->getContext($chatId);
        if (empty($context->stops)) {
            return ViewDataCollection::createWithSingleViewData(new PlanSaveResultViewData($update->callbackQuery->id));
        }

        $tripPlan = $this->planBuilderService->buildPlan($context);
        $title = $tripPlan->stops[0]->countryName. ', '.$tripPlan->stops[0]->cityName;

        $user = $this->userRepository->findOrCreateFromTelegramUpdate($update);

        $trip = $this->tripPersister->persistFromPlan($tripPlan, $user);
        $this->stateStorage->clearContext($chatId);

        return ViewDataCollection::createWithSingleViewData(new PlanSaveResultViewData($update->callbackQuery->id, $trip->getTitle() ?? $title));
    }
}
