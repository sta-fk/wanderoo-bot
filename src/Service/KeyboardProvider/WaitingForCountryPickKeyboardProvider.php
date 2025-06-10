<?php

namespace App\Service\KeyboardProvider;

use App\Enum\CallbackQueryData;
use App\Enum\States;

readonly class WaitingForCountryPickKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForCountryPick === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Оберіть країну:";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        $keyboard = [];
        foreach ($keyboardItems as $country) {
            $keyboard[] = [
                [
                    'text' => $country->name,
                    'callback_data' => CallbackQueryData::Country->value . $country->placeId,
                ],
            ];
        }

        return ['inline_keyboard' => $keyboard];
    }
}
