<?php

namespace App\Service\KeyboardService;

use App\Enum\CallbackQueryData;

trait GetDurationKeyboardTrait
{
    private function getDurationKeyboard(CallbackQueryData $callbackQueryData): array
    {
        if (
            $callbackQueryData !== CallbackQueryData::Duration
            && $callbackQueryData !== CallbackQueryData::StopDuration
        ) {
            return ['inline_keyboard' => []];
        }

        return [
            'inline_keyboard' => [
                [['text' => '1 день', 'callback_data' => $callbackQueryData->value.'1']],
                [['text' => '3 дні', 'callback_data' => $callbackQueryData->value.'3']],
                [['text' => '5 днів', 'callback_data' => $callbackQueryData->value.'5']],
                [['text' => '7 днів', 'callback_data' => $callbackQueryData->value.'7']],
                [['text' => '🔢 Інший варіант', 'callback_data' => $callbackQueryData->value.'custom']],
            ]
        ];
    }
}
