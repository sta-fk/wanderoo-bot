<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\FlowStepService\StartFlowStepService\InterestsService;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;
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

        return "Чудово! Ви обрали інтереси: " . implode(', ', $context->currentStopDraft->getInterestsLabels()) . ".\n\n💰 Тепер оберіть орієнтовний бюджет на подорож:";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        $budgetKeyboard = [];
        foreach ($keyboardItems as $callback => $label) {
            $budgetKeyboard[] = [[
                'text' => $label,
                'callback_data' => CallbackQueryData::Budget->value . $callback,
            ]];
        }

        return ['inline_keyboard' => $budgetKeyboard];
    }
}
