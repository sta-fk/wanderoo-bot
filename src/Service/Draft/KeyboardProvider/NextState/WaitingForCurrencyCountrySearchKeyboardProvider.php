<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;

readonly class WaitingForCurrencyCountrySearchKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForCurrencyCountrySearch === $requiredState;
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
