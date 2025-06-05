<?php

namespace App\Service\FlowStepService;

use App\DTO\Keyboard;

trait BuildKeyboardTrait
{
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
