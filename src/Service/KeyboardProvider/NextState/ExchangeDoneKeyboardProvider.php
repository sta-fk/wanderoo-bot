<?php

namespace App\Service\KeyboardProvider\NextState;

use App\Enum\States;
use App\Service\UserStateStorage;

readonly class ExchangeDoneKeyboardProvider implements NextStateKeyboardProviderInterface
{
    private ?string $fromAmount;
    private ?string $fromCurrency;
    private ?string $toAmount;
    private ?string $toCurrency;

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::ExchangeDone === $requiredState;
    }

    public function setCurrencyConversion(?string $fromAmount, ?string $fromCurrency, ?string $toAmount, ?string $toCurrency): self
    {
        $this->fromAmount = $fromAmount;
        $this->fromCurrency = $fromCurrency;
        $this->toAmount = $toAmount;
        $this->toCurrency = $toCurrency;

        return $this;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (is_null($this->fromCurrency) || is_null($this->toCurrency) || is_null($this->fromAmount) || is_null($this->toAmount)) {
            $context = $this->userStateStorage->getContext($chatId);

            return sprintf(
                "🔁 Бюджет було перераховано. \n\n💰У <b>%s</b> становить <b>%s</b>",
                $context->currency,
                $context->totalBudget . ' ' . $context->currency,
            );
        }

        return sprintf(
            "🔁 Бюджет було перераховано у <b>%s</b>\n\n💰 <b>%s → %s</b>",
            $this->toCurrency,
            $this->fromAmount . ' ' . $this->fromCurrency,
            $this->toAmount . ' ' . $this->toCurrency
        );
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return null;
    }
}
