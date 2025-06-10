<?php

namespace App\Service\KeyboardProvider;

use App\Enum\CallbackQueryData;
use App\Enum\States;

readonly class WaitingForCityPickKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForCityPick === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Оберіть місто:";
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
