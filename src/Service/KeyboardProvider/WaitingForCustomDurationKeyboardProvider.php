<?php

namespace App\Service\KeyboardProvider;

use App\Enum\States;

readonly class WaitingForCustomDurationKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForCustomDuration === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Введіть кількість днів (наприклад, 4):";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return null;
    }

    public function getValidationFailedMessage(): string
    {
        return "Будь ласка, введіть число від 1 до 30.";
    }
}
