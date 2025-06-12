<?php

namespace App\Service\KeyboardProvider\Callback;

use App\DTO\Request\TelegramCallbackQuery;
use App\Enum\CallbackQueryData;
use App\Service\KeyboardProvider\AddStopKeyboardProvider;

readonly class AddStopCallbackKeyboardProvider implements CallbackKeyboardProviderInterface
{
    public function __construct(
        private AddStopKeyboardProvider $keyboardProvider,
    ) {
    }

    public function supports(TelegramCallbackQuery $callbackQuery): bool
    {
        return $callbackQuery->data === CallbackQueryData::AddStop->value;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return $this->keyboardProvider->getTextMessage($chatId);
    }

    public function buildKeyboard(int $chatId = 0): ?array
    {
        return $this->keyboardProvider->buildKeyboard($chatId);
    }
}
