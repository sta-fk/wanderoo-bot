<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;

readonly class WaitingForDurationKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool
    {
        return States::WaitingForDuration === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Чудово! Тепер оберіть тривалість перебування у місті (днів):";
    }

    public function buildKeyboard(array $keyboardItems = [], array $selectedItems = []): ?array
    {
        return [
            'inline_keyboard' => [
                [['text' => '1 день', 'callback_data' => CallbackQueryData::Duration->value . '1']],
                [['text' => '3 дні', 'callback_data' => CallbackQueryData::Duration->value . '3']],
                [['text' => '5 днів', 'callback_data' => CallbackQueryData::Duration->value . '5']],
                [['text' => '7 днів', 'callback_data' => CallbackQueryData::Duration->value . '7']],
                [['text' => '🔢 Інший варіант', 'callback_data' => CallbackQueryData::Duration->value . CallbackQueryData::Custom->value]],
            ]
        ];
    }
}
