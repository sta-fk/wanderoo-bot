<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;
use App\Service\UserStateStorage;

readonly class WaitingForReuseOrNewTripStyleKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::WaitingForReuseOrNewTripStyle === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (0 === $chatId) {
            throw new \LogicException("Keyboard is not configured");
        }

        $context = $this->userStateStorage->getContext($chatId);
        $lastOneTripStyle = ($context->stops[count($context->stops) - 1])->getTripStyleLabel();

        return "Стиль минулої подорожі $lastOneTripStyle. Бажаєте зберегти для цієї?";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return [
            'inline_keyboard' => [[
                ['text' => '✅ Так', 'callback_data' => CallbackQueryData::TripStyle->value . CallbackQueryData::Reuse->value],
                ['text' => '❌ Ні', 'callback_data' => CallbackQueryData::TripStyle->value . CallbackQueryData::New->value],
            ]]
        ];
    }
}
