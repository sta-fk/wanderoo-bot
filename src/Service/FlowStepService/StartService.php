<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;

class StartService implements FlowStepServiceInterface
{
    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text === TelegramCommands::Start->value;
    }

    public function getNextState(): States
    {
        return States::WaitingForStart;
    }

    public function buildMessage(TelegramUpdate $update): SendMessageContext
    {
        return $this->buildWelcomeMessage($update->message->chat->id);
    }

    private function buildWelcomeMessage(int $chatId): SendMessageContext
    {
        $text = <<<TEXT
Привіт! Я ✈️ Wanderoo — бот, що допоможе спланувати твою мандрівку.

Я поставлю кілька простих запитань і згенерую персональний тревел-план: що подивитись, куди сходити, що скуштувати 🍜

Почнемо?
TEXT;

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🧳 Так, хочу план!', 'callback_data' => CallbackQueryData::StartYes->value],
                    ['text' => '❌ Ні, просто дивлюсь', 'callback_data' => CallbackQueryData::StartNo->value],
                ],
            ],
        ];

        return new SendMessageContext($chatId, $text, $keyboard);
    }
}
