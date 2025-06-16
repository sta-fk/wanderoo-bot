<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;

readonly class WaitingForCitySearchKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForCitySearch === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Введіть назву міста (або частину назви):";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return null;
    }
}
