<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\GetReadyToBuildPlanKeyboardTrait;
use App\Service\UserStateStorage;

readonly class CustomBudgetService implements StateAwareFlowStepServiceInterface
{
    use GetReadyToBuildPlanKeyboardTrait;

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text
            && $this->userStateStorage->getState($update->message->chat->id) === States::WaitingForCustomBudget;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCustomBudget];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $userInput = preg_replace('/[^\d]/', '', $update->message->text);

        if (!is_numeric($userInput)) {
            return new SendMessageContext(
                $chatId,
                "Не вдалося перетворити на цифру. Повторіть спробу.",
                null,
                States::WaitingForCustomBudget
            );
        }

        $context->currentStopDraft->budget = $userInput;
        $context->disableAddingStopFlow();

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "✅ Дякую! Орієнтовний бюджет: {$context->budget}€ +/-.\n\nГотуємо для вас персоналізований план мандрівки... ✈️",
            $this->getBuildPlanKeyboard(),
            States::ReadyToBuildPlan
        );
    }
}
