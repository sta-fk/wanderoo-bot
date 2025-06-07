<?php

namespace App\Service\KeyboardService;

use App\Enum\CallbackQueryData;

trait GetTripStyleKeyboardTrait
{
    private function getTripStyleKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '🧘 Лайтовий', 'callback_data' => CallbackQueryData::TripStyle->value . 'лайтовий'],
                    ['text' => '🚀 Активний', 'callback_data' => CallbackQueryData::TripStyle->value . 'активний'],
                    ['text' => '🎭 Змішаний', 'callback_data' => CallbackQueryData::TripStyle->value . 'змішаний'],
                ],
            ]
        ];
    }
}
