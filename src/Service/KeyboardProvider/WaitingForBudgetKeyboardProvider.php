<?php

namespace App\Service\KeyboardProvider;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StartFlowStepService\BudgetService;
use App\Service\FlowStepService\StartFlowStepService\InterestsService;
use App\Service\UserStateStorage;

readonly class WaitingForBudgetKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::WaitingForBudget === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (0 === $chatId) {
            throw new \LogicException("Keyboard is not configured");
        }

        $context = $this->userStateStorage->getContext($chatId);
        $selectedLabels = array_map(
            static fn ($key) => strtolower(InterestsService::INTERESTS[$key]) ?? $key,
            $context->currentStopDraft->interests ?? []
        );

        return "Чудово! Ви обрали інтереси: " . implode(', ', $selectedLabels) . ".\n\n💰 Тепер оберіть орієнтовний бюджет на подорож:";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        $budgetKeyboard = [];
        foreach (BudgetService::BUDGET_OPTIONS as $callback => $label) {
            $budgetKeyboard[] = [[
                'text' => $label,
                'callback_data' => CallbackQueryData::Budget->value . $callback,
            ]];
        }

        return ['inline_keyboard' => $budgetKeyboard];
    }
}
