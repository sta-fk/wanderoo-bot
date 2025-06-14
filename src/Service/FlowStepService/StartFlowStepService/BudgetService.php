<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Budget\BudgetHelperService;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class BudgetService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
        private BudgetHelperService $budgetHelper,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Budget->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForBudget];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $budgetKey = substr($update->callbackQuery->data, strlen(CallbackQueryData::Budget->value));

        if (CallbackQueryData::Custom->value === $budgetKey) {
            $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCustomBudget);

            return new SendMessageContext(
                $chatId,
                $nextStateKeyboardProvider->getTextMessage($chatId),
                $nextStateKeyboardProvider->buildKeyboard(),
                States::WaitingForCustomBudget
            );
        }

        $context->currentStopDraft->budget = $budgetKey;

        // ⬇️ NEW: розрахунок бюджету в валюті загального плану
        $range = $this->budgetHelper->resolveBudgetRange($budgetKey, $context->currency);
        if ($range !== null) {
            [$minBudget, $maxBudget] = $range;
            $avgBudget = $maxBudget ? ($minBudget + $maxBudget) / 2 : $minBudget;

            $this->budgetHelper->applyBudgetToStop($context->currentStopDraft, $context, $avgBudget);
        }

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
