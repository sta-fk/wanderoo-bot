<?php

namespace App\Service\Draft\KeyboardProvider\Callback;

use App\DTO\Request\TelegramCallbackQuery;
use App\Enum\CallbackQueryData;
use App\Service\Draft\KeyboardProvider\Callback\CallbackKeyboardProviderInterface;

readonly class ViewTripPlanKeyboardProvider implements CallbackKeyboardProviderInterface
{
    private string $viewTripPlanId;

    public function supports(TelegramCallbackQuery $callbackQuery): bool
    {
        return str_starts_with($callbackQuery->data, CallbackQueryData::ViewTrip->value);
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return 'Що робити з цим планом?';
    }

    public function buildKeyboard(int $chatId = 0): ?array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '✏️ Змінити', 'callback_data' => CallbackQueryData::EditTrip->value . $this->viewTripPlanId],
                    ['text' => '🗑️ Видалити', 'callback_data' => CallbackQueryData::DeleteTrip->value . $this->viewTripPlanId],
                ],
                [
                    ['text' => '⬅️ Назад', 'callback_data' => CallbackQueryData::ViewAllTrips->value],
                ]
            ]
        ];
    }

    public function setViewTripPlanId(string $viewTripPlanId): self
    {
        $this->viewTripPlanId = $viewTripPlanId;
        return $this;
    }
}
