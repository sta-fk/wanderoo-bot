<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;

readonly class WaitingForCountryKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return $requiredState === States::WaitingForCountry;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Супер, поїхали ✨! Введіть назву країни (або частину назви):";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return null;
    }
}
