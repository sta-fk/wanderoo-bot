<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;

readonly class WaitingForCountryPickKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForCountryPick === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Країни за вашим запитом:";
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
