<?php

namespace App\Service\KeyboardService;

use App\DTO\Keyboard;
use App\Enum\CallbackQueryData;

trait BuildKeyboardTrait
{
    private function buildKeyboard(Keyboard $keyboard): array
    {
        $buttons = [];
        foreach ($keyboard->items as $item) {
            $buttons[][] = [
                'text' => $item[$keyboard->textField],
                'callback_data' => $keyboard->prefix . $item[$keyboard->keyField],
            ];
        }
        return ['inline_keyboard' => $buttons];
    }
}
