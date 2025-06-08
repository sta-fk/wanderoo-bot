<?php

namespace App\Service\KeyboardService;

use App\Enum\CallbackQueryData;

trait GetConfirmStopKeyboardTrait
{
    private function getConfirmKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Підтвердити зупинку', 'callback_data' => CallbackQueryData::ConfirmStop->value],
                    ['text' => '➕ Додати ще зупинку', 'callback_data' => CallbackQueryData::AddStop->value],
                ],
            ],
        ];
    }
}
