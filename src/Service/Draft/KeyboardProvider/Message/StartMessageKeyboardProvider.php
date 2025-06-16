<?php

namespace App\Service\Draft\KeyboardProvider\Message;

use App\DTO\Request\TelegramMessage;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Service\Draft\KeyboardProvider\Message\MessageKeyboardProviderInterface;
use App\Service\UserStateStorage;

readonly class StartMessageKeyboardProvider implements MessageKeyboardProviderInterface
{
    public function supports(TelegramMessage $message): bool
    {
        return $message->text === TelegramCommands::Start->value;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Привіт! Я ✈️ Wanderoo — бот, що допоможе спланувати твою мандрівку. \n\nЯ поставлю кілька простих запитань і згенерую персональний тревел-план: що подивитись, куди сходити, що скуштувати 🍜 \n\n Почнемо?";
    }

    public function buildKeyboard(int $chatId = 0): ?array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '🧳 Так, хочу план!', 'callback_data' => CallbackQueryData::StartYes->value],
                    ['text' => '❌ Ні, просто дивлюсь', 'callback_data' => CallbackQueryData::StartNo->value],
                ],
            ],
        ];
    }
}
