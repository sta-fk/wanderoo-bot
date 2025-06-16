<?php

namespace App\Service\Draft\KeyboardProvider\Callback;

use App\DTO\Request\TelegramCallbackQuery;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Service\Draft\KeyboardProvider\Callback\CallbackKeyboardProviderInterface;

readonly class NewTripCallbackKeyboardProvider implements CallbackKeyboardProviderInterface
{
    public function supports(TelegramCallbackQuery $callbackQuery): bool
    {
        return CallbackQueryData::NewTrip->value === $callbackQuery->data;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Розпочнімо нову подорож! 🌍\n\nСпершу введіть назву країни (або частину назви):";
    }

    public function buildKeyboard(int $chatId = 0): ?array
    {
        return null;
    }
}
