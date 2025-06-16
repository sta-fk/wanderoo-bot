<?php

namespace App\Service\Draft\KeyboardProvider\Callback;

use App\DTO\Request\TelegramCallbackQuery;
use App\Enum\CallbackQueryData;
use App\Service\Draft\KeyboardProvider\Callback\CallbackKeyboardProviderInterface;

readonly class GeneratingTripPlanKeyboardProvider implements CallbackKeyboardProviderInterface
{
    public function supports(TelegramCallbackQuery $callbackQuery): bool
    {
        return CallbackQueryData::GeneratingTripPlan->value === $callbackQuery->data;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return "Що бажаєте зробити з цим маршрутом?";
    }

    public function buildKeyboard(int $chatId = 0): ?array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Зберегти план', 'callback_data' => CallbackQueryData::SaveGeneratedPlan->value],
                ],
                [
                    ['text' => '✏️ Змінити план', 'callback_data' => CallbackQueryData::EditGeneratedPlan->value],
                ],
                [
                    ['text' => '🔄 Почати заново', 'callback_data' => CallbackQueryData::NewTrip->value],
                ],
                [
                    ['text' => '🔙 Назад', 'callback_data' => CallbackQueryData::BackToMainMenu->value],
                ],
            ]
        ];
    }
}
