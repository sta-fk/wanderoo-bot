<?php

namespace App\Service\KeyboardProvider;

use App\DTO\PlanContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\UserStateStorage;

readonly class WaitingForCustomBudgetKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private CurrencyExchangerService $currencyExchangerService,
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
            $potentialAmount = $this->currencyExchangerService->convert(500, CallbackQueryData::Usd->value, $context->currency);

            return "✍️ Введіть бажаний бюджет у {$context->currency} (наприклад: <b>{$potentialAmount}</b>):";
        }

        return $this->getTextMessageAfterStates($context, $this->userStateStorage->getState($chatId));
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return null;
    }

    private function getTextMessageAfterStates(PlanContext $context, States $uncompletedState): string
    {
        $potentialAmount = round($this->currencyExchangerService->convert(100, CallbackQueryData::Usd->value, $context->currency), -1);

        return match ($uncompletedState) {
            States::WaitingForCurrencyChoice,
            States::WaitingForCurrencyCountryPick => "Валюта плану встановлена: {$context->currency}. \n\n✍️ Введіть бажаний бюджет у {$context->currency} (наприклад: <b>{$potentialAmount}</b>):",
            States::WaitingForInterests,
            States::WaitingForReuseOrNewInterests => "Чудово! Інтереси для {$context->currentStopDraft->cityName}: " . implode(', ', $context->currentStopDraft->getInterestsLabels()) . ".\n\n✍️ Введіть бажаний бюджет у {$context->currency} (наприклад: <b>{$potentialAmount}</b>):",
            default => throw new \LogicException("Keyboard is not configured"),
        };
    }
}
