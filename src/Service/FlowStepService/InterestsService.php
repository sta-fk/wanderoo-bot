<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

readonly class InterestsService implements StatefulFlowStepServiceInterface
{
    private const INTERESTS = [
        'city' => '🏙️ Міста',
        'nature' => '🏞️ Природа',
        'food' => '🍽️ Їжа',
        'culture' => '🎭 Культура',
        'shopping' => '🛍️ Шопінг',
        'beach' => '🏖️ Пляжний відпочинок',
    ];

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && (str_starts_with($update->callbackQuery->data, CallbackQueryData::Interest->value)
                || $update->callbackQuery->data === CallbackQueryData::InterestDone->value);
    }

    public function getNextState(): States
    {
        return States::WaitingForNextStep;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $interestKey = substr($update->callbackQuery->data, strlen(CallbackQueryData::Interest->value));

        if (in_array($interestKey, $context->interests, true)) {
            $context->interests = array_filter($context->interests, static fn ($i) => $i !== $interestKey);
        } else {
            $context->interests[] = $interestKey;
        }

        $this->userStateStorage->saveContext($chatId, $context);

        $keyboard = $this->buildInterestsKeyboard($context->interests);

        return new SendMessageContext($chatId, "✨ Що вас цікавить у подорожі? Оберіть кілька варіантів:", $keyboard);
    }

    private function buildInterestsKeyboard(array $selectedInterests): array
    {
        $buttons = [];

        foreach (self::INTERESTS as $key => $label) {
            $isSelected = in_array($key, $selectedInterests, true);
            $buttonText = ($isSelected ? '✅ ' : '⬜️ ') . $label;

            $buttons[][] = [
                'text' => $buttonText,
                'callback_data' => CallbackQueryData::Interest->value . $key,
            ];
        }

        $buttons[][] = [
            [
                'text' => '✅ Готово',
                'callback_data' => CallbackQueryData::InterestDone->value,
            ],
        ];

        return ['inline_keyboard' => $buttons];
    }
}
