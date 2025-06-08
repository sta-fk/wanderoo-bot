<?php

namespace App\Service\KeyboardService;

use App\DTO\Keyboard;
use App\Enum\CallbackQueryData;

trait BuildInterestsKeyboardTrait
{
    private function buildInterestsKeyboard(CallbackQueryData $callbackQueryData, CallbackQueryData $callbackQueryDataDone, array $selectedInterests, array $interestsSet): array
    {
        if (
            ($callbackQueryData !== CallbackQueryData::Interest && $callbackQueryDataDone !== CallbackQueryData::InterestsDone)
            && ($callbackQueryData !== CallbackQueryData::StopInterest && $callbackQueryDataDone !== CallbackQueryData::StopInterestsDone)
        ) {
            return ['inlineKeyboard' => []];
        }

        $buttons = [];

        foreach ($interestsSet as $key => $label) {
            $isSelected = in_array($key, $selectedInterests, true);
            $buttonText = ($isSelected ? '✅ ' : '⬜️ ') . $label;

            $buttons[][] = [
                'text' => $buttonText,
                'callback_data' => $callbackQueryData->value . $key,
            ];
        }

        $buttons[][] = [
                'text' => '✅ Готово',
                'callback_data' => $callbackQueryDataDone->value,
            ];

        return ['inline_keyboard' => $buttons];
    }
}
