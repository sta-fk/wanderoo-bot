<?php

namespace App\Service\KeyboardProvider;

use App\Enum\CallbackQueryData;
use App\Enum\States;

class WaitingForCurrencyCountryPickKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForCurrencyCountryPick === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Оберіть країну, валюту якої буде присвоєно:";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        $keyboard = [];
        foreach ($keyboardItems as $country) {
            $keyboard[] = [
                [
                    'text' => $country->name,
                    'callback_data' => CallbackQueryData::CurrencyCountryPick->value . $country->placeId,
                ],
            ];
        }

        return ['inline_keyboard' => $keyboard];
    }
}
