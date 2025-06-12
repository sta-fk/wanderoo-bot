<?php

namespace App\Service\KeyboardProvider\Message;

use App\DTO\Request\TelegramMessage;
use App\Enum\TelegramCommands;
use App\Service\KeyboardProvider\AddStopKeyboardProvider;
use App\Service\KeyboardProvider\Message\MessageKeyboardProviderInterface;

readonly class AddStopMessageKeyboardProvider implements MessageKeyboardProviderInterface
{
    public function __construct(
        private AddStopKeyboardProvider $keyboardProvider,
    ) {
    }

    public function supports(TelegramMessage $message): bool
    {
        return $message->text === TelegramCommands::AddStop->value;
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
