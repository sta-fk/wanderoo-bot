<?php

namespace App\Service\KeyboardService;

use App\DTO\Keyboard;
use App\Enum\CallbackQueryData;

trait BuildPaginationKeyboardTrait
{
    private const DEFAULT_PAGINATION_LIMIT = 5;

    private function buildPaginationKeyboard(Keyboard $keyboard): array
    {
        $buttons = [];

        foreach ($keyboard->items as $item) {
            $buttons[][] = [
                'text' => $item[$keyboard->textField],
                'callback_data' => $keyboard->prefix . $item[$keyboard->keyField],
            ];
        }

        if ($keyboard->nextPageOffset !== null) {
            $buttons[][] = [
                'text' => '➡️ Наступна сторінка',
                'callback_data' => $keyboard->paginationPrefix . $keyboard->nextPageOffset,
            ];
        }

        return ['inline_keyboard' => $buttons];
    }
}
