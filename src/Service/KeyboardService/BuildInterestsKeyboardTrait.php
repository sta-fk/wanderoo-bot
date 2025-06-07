<?php

namespace App\Service\KeyboardService;

use App\DTO\Keyboard;
use App\Enum\CallbackQueryData;

trait BuildInterestsKeyboardTrait
{
    private function buildInterestsKeyboard(array $selectedInterests, array $interestsSet): array
    {
        $buttons = [];

        foreach ($interestsSet as $key => $label) {
            $isSelected = in_array($key, $selectedInterests, true);
            $buttonText = ($isSelected ? '✅ ' : '⬜️ ') . $label;

            $buttons[][] = [
                'text' => $buttonText,
                'callback_data' => CallbackQueryData::Interest->value . $key,
            ];
        }

        $buttons[][] = [
                'text' => '✅ Готово',
                'callback_data' => CallbackQueryData::InterestsDone->value,
            ];

        return ['inline_keyboard' => $buttons];
    }
}
