<?php

namespace App\Service\KeyboardService;

use App\Enum\CallbackQueryData;

trait GetDurationKeyboardTrait
{
    private function getDurationKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [['text' => '1 день', 'callback_data' => CallbackQueryData::Duration->value.'1']],
                [['text' => '3 дні', 'callback_data' => CallbackQueryData::Duration->value.'3']],
                [['text' => '5 днів', 'callback_data' => CallbackQueryData::Duration->value.'5']],
                [['text' => '7 днів', 'callback_data' => CallbackQueryData::Duration->value.'7']],
                [['text' => '🔢 Інший варіант', 'callback_data' => CallbackQueryData::Duration->value.'custom']],
            ]
        ];
    }
}
