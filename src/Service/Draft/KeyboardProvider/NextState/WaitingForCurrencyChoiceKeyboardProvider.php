<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;

readonly class WaitingForCurrencyChoiceKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForCurrencyChoice === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Валюта в цій країні відрізняється від інших обраних країн, де ви перебуватиме. \n\nОберіть найбільш зручну валюту для генерації плану:";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return [
            'inline_keyboard' => [
                [['text' => '🇺🇸 USD долар', 'callback_data' => CallbackQueryData::CurrencyChoice->value . CallbackQueryData::Usd->value]],
                [['text' => '🇪🇺 EUR євро', 'callback_data' => CallbackQueryData::CurrencyChoice->value . CallbackQueryData::Eur->value]],
                [['text' => '🌍 Вибрати валюту за країною', 'callback_data' => CallbackQueryData::CurrencyChoice->value . CallbackQueryData::FromCountry->value]],
            ]
        ];
    }
}
