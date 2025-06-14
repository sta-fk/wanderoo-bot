<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Service\KeyboardResolver\KeyboardProviderResolver;
use App\Service\TripPlanner\PlanBuilderService;
use App\Service\TripPlanner\TripPlanFormatterInterface;
use App\Service\UserStateStorage;

readonly class GeneratingTripPlanService implements FinalStateAwareFlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlanBuilderService $planBuilderService,
        private TripPlanFormatterInterface $tripPlanFormatter,
        private KeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::GeneratingTripPlan->value === $update->callbackQuery->data
        ;
    }

    public function getSplitFormattedPlan(TelegramUpdate $update): array
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $context->finishCreatingNewStop();
        $context->disableAddingStopFlow();

        $this->userStateStorage->saveContext($chatId, $context);

        $tripPlan = $this->planBuilderService->buildPlan($context);

        $texts = $this->tripPlanFormatter->splitFormattedPlan($tripPlan);

        $i = 0;
        $messages = [];
        while ($i < count($texts)) {
            $messages[] = new SendMessageContext(
                $chatId,
                $texts[$i],
            );
            $i++;
        }

        // Final message with keyboard
        $messages[] = $this->buildNextStepMessage($update);

        return $messages;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $keyboardProvider = $this->keyboardProviderResolver->resolve($update);

        return new SendMessageContext(
            $update->callbackQuery->message->chat->id,
            $keyboardProvider->getTextMessage(),
            $keyboardProvider->buildKeyboard(),
        );
    }
}
