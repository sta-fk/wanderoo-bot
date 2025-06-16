<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;

class WaitingForExchangeCountryPickKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForExchangeCountryPick === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Оберіть країну, валюту якої буде встановлено для планування:";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        $keyboard = [];
        foreach ($keyboardItems as $country) {
            $keyboard[] = [
                [
                    'text' => $country->name,
                    'callback_data' => CallbackQueryData::ExchangeCountryPick->value . $country->placeId,
                ],
            ];
        }

        return ['inline_keyboard' => $keyboard];
    }
}
