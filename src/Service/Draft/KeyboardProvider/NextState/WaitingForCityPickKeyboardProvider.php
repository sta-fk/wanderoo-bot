<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;

readonly class WaitingForCityPickKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForCityPick === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Міста за вашим запитом:";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        $keyboard = [];
        foreach ($keyboardItems as $city) {
            $keyboard[] = [
                [
                    'text' => $city->name,
                    'callback_data' => CallbackQueryData::City->value . $city->placeId,
                ],
            ];
        }

        return ['inline_keyboard' => $keyboard];
    }
}
