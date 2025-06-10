<?php

namespace App\Service\KeyboardProvider;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

readonly class WaitingForTripStyleKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::WaitingForTripStyle === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (0 === $chatId) {
            throw new \LogicException("Keyboard is not configured");
        }

        $context = $this->userStateStorage->getContext($chatId);
        if ($context->isAddingStopFlow) {
            return "Який стиль подорожі ви бажаєте? 🧳";
        }

        return "✅ Подорож з <b>{$context->startDate->format('Y-m-d')}</b> по <b>{$context->endDate->format('Y-m-d')}</b> \n\nЯкий стиль подорожі ви бажаєте? 🧳";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '🧘 Лайтовий', 'callback_data' => CallbackQueryData::TripStyle->value . 'лайтовий'],
                    ['text' => '🚀 Активний', 'callback_data' => CallbackQueryData::TripStyle->value . 'активний'],
                    ['text' => '🎭 Змішаний', 'callback_data' => CallbackQueryData::TripStyle->value . 'змішаний'],
                ],
            ]
        ];
    }
}
