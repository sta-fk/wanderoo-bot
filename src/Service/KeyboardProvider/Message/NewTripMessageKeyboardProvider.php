<?php

namespace App\Service\KeyboardProvider\Message;

use App\DTO\Request\TelegramMessage;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Service\KeyboardProvider\Message\MessageKeyboardProviderInterface;
use App\Service\UserStateStorage;

readonly class NewTripMessageKeyboardProvider implements MessageKeyboardProviderInterface
{
    public function supports(TelegramMessage $message): bool
    {
        return $message->text === TelegramCommands::NewTrip->value;
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
