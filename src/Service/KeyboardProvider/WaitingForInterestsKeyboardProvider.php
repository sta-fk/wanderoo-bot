<?php

namespace App\Service\KeyboardProvider;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StartFlowStepService\InterestsService;
use App\Service\UserStateStorage;

readonly class WaitingForInterestsKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::WaitingForInterests === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (0 === $chatId) {
            throw new \LogicException("Keyboard is not configured");
        }

        $context = $this->userStateStorage->getContext($chatId);

        return "Стиль цієї подорожі: <b>{$context->currentStopDraft->tripStyle}</b>.\n\nНаступний крок...\n\n✨ Що вас цікавить в {$context->currentStopDraft->cityName}? Оберіть кілька варіантів:";
    }

    public function buildKeyboard(array $keyboardItems = []): array
    {
        $buttons = [];

        foreach (InterestsService::INTERESTS as $key => $label) {
            $isSelected = in_array($key, $keyboardItems, true);
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
