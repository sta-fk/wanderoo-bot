<?php

namespace App\Service\KeyboardService;

use App\Enum\CallbackQueryData;

trait BuildBudgetKeyboardTrait
{
    private function buildBudgetKeyboard(array $budgetOptions): array
    {
        $budgetKeyboard = [];
        foreach ($budgetOptions as $callback => $label) {
            $budgetKeyboard[] = [[
                'text' => $label,
                'callback_data' => CallbackQueryData::Budget->value . $callback,
            ]];
        }

        return ['inline_keyboard' => $budgetKeyboard];
    }
}
