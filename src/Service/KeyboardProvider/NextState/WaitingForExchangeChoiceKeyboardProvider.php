<?php

namespace App\Service\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;

readonly class WaitingForExchangeChoiceKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForExchangeChoice === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return 'Оберіть валюту для перерахунку бюджету:';
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return [
            'inline_keyboard' => [
                [['text' => '🇺🇸 USD долар', 'callback_data' => CallbackQueryData::ExchangeChoice->value . CallbackQueryData::Usd->value]],
                [['text' => '🇪🇺 EUR євро', 'callback_data' => CallbackQueryData::ExchangeChoice->value . CallbackQueryData::Eur->value]],
                [['text' => '🌍 Вибрати валюту за країною', 'callback_data' => CallbackQueryData::ExchangeChoice->value . CallbackQueryData::FromCountry->value]],
            ]
        ];
    }
}
