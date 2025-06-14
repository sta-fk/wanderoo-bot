<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Repository\UserRepository;
use App\Service\FlowStepServiceInterface;
use App\Service\TripPersister;
use App\Service\TripPlanner\PlanBuilderService;
use App\Service\UserStateStorage;

readonly class SaveGeneratedPlanService implements FlowStepServiceInterface
{
    public function __construct(
        private TripPersister $tripPersister,
        private UserRepository $userRepository,
        private UserStateStorage $stateStorage,
        private PlanBuilderService $planBuilderService,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::SaveGeneratedPlan->value === $update->callbackQuery->data
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;

        $context = $this->stateStorage->getContext($chatId);
        if (empty($context->stops)) {
            return new SendMessageContext(
                $chatId,
                "😔 Немає згенерованого плану для збереження."
            );
        }

        $tripPlan = $this->planBuilderService->buildPlan($context);

        $user = $this->userRepository->findOrCreateFromTelegramUser($update->callbackQuery->message);

        $trip = $this->tripPersister->persistFromPlan($tripPlan, $user);
        $this->stateStorage->clearContext($chatId);

        return new SendMessageContext(
            $chatId,
            sprintf("✅ План '%s' збережено!", $trip->getTitle())
        );
    }
}
