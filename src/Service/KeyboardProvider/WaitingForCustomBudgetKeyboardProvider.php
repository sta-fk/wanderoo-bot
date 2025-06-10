<?php

namespace App\Service\KeyboardProvider;

use App\Enum\States;
use App\Service\FlowStepService\StartFlowStepService\InterestsService;
use App\Service\UserStateStorage;

readonly class WaitingForCustomBudgetKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::WaitingForCustomBudget === $requiredState;
    }

    public function getValidationFailedMessage(): string
    {
        return "Не вдалося перетворити на цифру. Повторіть спробу.";
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (0 === $chatId) {
            throw new \LogicException("Keyboard is not configured");
        }

        $context = $this->userStateStorage->getContext($chatId);

        if (!$context->isAddingStopFlow) {
            return "✍️ Введіть бажаний бюджет у євро (наприклад: <b>500</b>):";
        }

        $selectedLabels = array_map(
            static fn ($key) => strtolower(InterestsService::INTERESTS[$key]) ?? $key,
            $context->currentStopDraft->interests ?? []
        );

        return "Чудово! Інтереси для {$context->currentStopDraft->cityName}: " . implode(', ', $selectedLabels) . ".\n\n✍️ Введіть бажаний бюджет у євро (наприклад: <b>100</b>):";

    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return null;
    }
}
