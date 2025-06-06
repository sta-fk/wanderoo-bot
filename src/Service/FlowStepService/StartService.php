<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;

class StartService implements StatefulFlowStepServiceInterface
{
    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text === TelegramCommands::Start->value;
    }

    public function getNextState(): States
    {
        return States::WaitingForStart;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $text = "Привіт! Я ✈️ Wanderoo — бот, що допоможе спланувати твою мандрівку. \n\nЯ поставлю кілька простих запитань і згенерую персональний тревел-план: що подивитись, куди сходити, що скуштувати 🍜 \n\n Почнемо?";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🧳 Так, хочу план!', 'callback_data' => CallbackQueryData::StartYes->value],
                    ['text' => '❌ Ні, просто дивлюсь', 'callback_data' => CallbackQueryData::StartNo->value],
                ],
            ],
        ];

        return new SendMessageContext($update->message->chat->id, $text, $keyboard);
    }
}
