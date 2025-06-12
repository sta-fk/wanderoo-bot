<?php

namespace App\Service\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StartFlowStepService\BudgetService;
use App\Service\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;
use App\Service\UserStateStorage;

readonly class ReadyToBuildPlanKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::ReadyToBuildPlan === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (0 === $chatId) {
            throw new \LogicException("Keyboard is not configured");
        }

        $context = $this->userStateStorage->getContext($chatId);
        if (null !== $context->currentStopDraft?->countryName) {
            $enteredBudget = $context->currentStopDraft->budgetInPlanCurrency;
        } elseif (!empty($context->stops)) {
            $enteredBudget = ($context->stops[count($context->stops) - 1])->budgetInPlanCurrency;
        } else {
            return "✅ Дякую! Тепер підтвердьте план подорожі або додайте ще одну зупинку... ✈️";
        }

        return "✅ Дякую! Орієнтовний бюджет: {$enteredBudget} {$context->currency}.\n\nТепер підтвердьте план подорожі або додайте ще одну зупинку... ✈️";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '➕ Додати зупинку', 'callback_data' => CallbackQueryData::AddStop->value],
                    ['text' => '✅ Завершити планування', 'callback_data' => CallbackQueryData::GeneratingTripPlan->value],
                ],
            ],
        ];
    }
}
