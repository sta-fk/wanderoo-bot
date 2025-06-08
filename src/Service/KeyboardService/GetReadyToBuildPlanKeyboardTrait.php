<?php

namespace App\Service\KeyboardService;

use App\Enum\CallbackQueryData;

trait GetReadyToBuildPlanKeyboardTrait
{
    private function getBuildPlanKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '➕ Додати зупинку', 'callback_data' => CallbackQueryData::AddStop->value],
                    ['text' => '✅ Завершити планування', 'callback_data' => CallbackQueryData::ReadyToBuildPlan->value],
                ],
            ],
        ];
    }
}
