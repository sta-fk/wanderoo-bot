<?php

namespace App\Service\KeyboardProvider;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

readonly class WaitingForReuseOrNewInterestsKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::WaitingForReuseOrNewInterests === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (0 === $chatId) {
            throw new \LogicException("Keyboard is not configured");
        }

        $context = $this->userStateStorage->getContext($chatId);

        return "Стиль подорожі для {$context->currentStopDraft->cityName}: <b>{$context->currentStopDraft->tripStyle}</b>.\n\nНаступний крок...\n\n✨ Використати попередні інтереси для цієї зупинки?";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        return [
            'inline_keyboard' => [
                ['text' => '✅ Так', 'callback_data' => CallbackQueryData::Interest->value . CallbackQueryData::Reuse->value],
                ['text' => '❌ Ні', 'callback_data' => CallbackQueryData::Interest->value . CallbackQueryData::New->value],
            ]
        ];
    }
}
