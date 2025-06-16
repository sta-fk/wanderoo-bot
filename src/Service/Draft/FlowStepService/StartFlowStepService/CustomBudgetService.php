<?php

namespace App\Service\Draft\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\Draft\Budget\BudgetHelperService;
use App\Service\Draft\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\Draft\KeyboardProvider\NextState\WaitingForCustomBudgetKeyboardProvider;
use App\Service\Draft\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class CustomBudgetService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
        private WaitingForCustomBudgetKeyboardProvider $customBudgetKeyboardProvider,
        private BudgetHelperService $budgetHelper,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->message?->text;
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
                $this->customBudgetKeyboardProvider->getValidationFailedMessage(),
                $this->customBudgetKeyboardProvider->buildKeyboard(),
                States::WaitingForCustomBudget
            );
        }

        // ⬇️ Конвертація бюджету в валюту плану
        $this->budgetHelper->applyBudgetToStop($context->currentStopDraft, $context, $userInput);

        $context->finishCreatingNewStop();
        $this->userStateStorage->saveContext($chatId, $context);

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::ReadyToBuildPlan);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage($chatId),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::ReadyToBuildPlan
        );
    }
}
