<?php

namespace App\Service\KeyboardProvider;

use App\DTO\PlanContext;
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
            return "✍️ Введіть бажаний бюджет у {$context->currency} (наприклад: <b>500</b>):";
        }

        return $this->getTextMessageAfterStates($context, $this->userStateStorage->getState($chatId));
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return null;
    }

    private function getTextMessageAfterStates(PlanContext $context, States $uncompletedState): string
    {
        $selectedLabels = array_map(
            static fn ($key) => strtolower(InterestsService::INTERESTS[$key]) ?? $key,
            $context->currentStopDraft->interests ?? []
        );

        return match ($uncompletedState) {
            States::WaitingForCurrencyChoice => "Валюта плану встановлена: {$context->currency}. \n\n✍️ Введіть бажаний бюджет у {$context->currency} (наприклад: <b>100</b>):",
            States::WaitingForInterests,
            States::WaitingForReuseOrNewInterests => "Чудово! Інтереси для {$context->currentStopDraft->cityName}: " . implode(', ', $selectedLabels) . ".\n\n✍️ Введіть бажаний бюджет у {$context->currency} (наприклад: <b>100</b>):",
            default => throw new \LogicException("Keyboard is not configured"),
        };
    }
}
