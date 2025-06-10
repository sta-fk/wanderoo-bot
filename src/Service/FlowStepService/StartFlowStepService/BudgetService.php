<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

class BudgetService implements StateAwareFlowStepServiceInterface
{
    public const BUDGET_OPTIONS = [
        'none' => 'Без бюджету',
        '0_300' => 'До 300€',
        '300_700' => '300€ — 700€',
        '700_1500' => '700€ — 1500€',
        '1500_plus' => 'Понад 1500€',
        'custom' => 'Ввести вручну',
    ];

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
        private readonly NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Budget->value)
        ;
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
