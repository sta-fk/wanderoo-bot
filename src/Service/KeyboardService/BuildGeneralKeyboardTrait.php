<?php

namespace App\Service\KeyboardService;

use App\DTO\Keyboard;
use App\Enum\CallbackQueryData;

trait BuildGeneralKeyboardTrait
{
    private function buildSimpleKeyboard(array $items, string $callbackPrefix, string $labelField, string $valueField): array
    {
        $keyboard = [];
        foreach ($items as $item) {
            $keyboard[] = [
                [
                    'text' => $item[$labelField],
                    'callback_data' => $callbackPrefix . $item[$valueField],
                ],
            ];
        }

        return ['inline_keyboard' => $keyboard];
    }
}
