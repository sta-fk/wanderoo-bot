<?php

namespace App\Service\KeyboardProvider\NextState;

use App\Enum\States;
use App\Service\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;

readonly class WaitingForExchangeCountrySearchKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForExchangeCountrySearch === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Введіть назву країни (або частину назви), <b>валюта</b> якої потрібна:";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return null;
    }
}
