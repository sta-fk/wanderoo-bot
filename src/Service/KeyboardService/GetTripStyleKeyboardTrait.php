<?php

namespace App\Service\KeyboardService;

use App\Enum\CallbackQueryData;

trait GetTripStyleKeyboardTrait
{
    private function getTripStyleKeyboard(CallbackQueryData $callbackQueryData): array
    {
        if ($callbackQueryData !== CallbackQueryData::TripStyle && $callbackQueryData !== CallbackQueryData::StopTripStyle) {
            return ['inlineKeyboard' => []];
        }

        return [
            'inline_keyboard' => [
                [
                    ['text' => '🧘 Лайтовий', 'callback_data' => $callbackQueryData->value . 'лайтовий'],
                    ['text' => '🚀 Активний', 'callback_data' => $callbackQueryData->value . 'активний'],
                    ['text' => '🎭 Змішаний', 'callback_data' => $callbackQueryData->value . 'змішаний'],
                ],
            ]
        ];
    }
}
