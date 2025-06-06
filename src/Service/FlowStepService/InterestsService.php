<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

readonly class InterestsService implements StatefulFlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public const INTERESTS = [
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
        return null !== $update->callbackQuery &&
            (str_starts_with($update->callbackQuery->data, CallbackQueryData::Interest->value) ||
                $update->callbackQuery->data === CallbackQueryData::InterestsDone->value);
    }

    public function getNextState(): States
    {
        return States::WaitingForNextStep;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {

        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $callbackData = $update->callbackQuery->data;

        if (CallbackQueryData::InterestsDone->value === $callbackData) {
            $selectedLabels = array_map(
                static fn($key) => strtolower(self::INTERESTS[$key]) ?? $key,
                $context->interests ?? []
            );

            return new SendMessageContext(
                $chatId,
                "Чудово! Ви обрали інтереси: " . implode(', ', $selectedLabels) . ". Наступний крок...",
            );
        }

        $selectedInterest = substr($callbackData, strlen(CallbackQueryData::Interest->value));
        if (!in_array($selectedInterest, $context->interests ?? [], true)) {
            $context->interests[] = $selectedInterest;
        } else {
            $context->interests = array_filter(
                $context->interests,
                static fn ($interest) => $interest !== $selectedInterest
            );
        }

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "✨ Оновлено. Щось ще?",
            $this->buildInterestsKeyboard($context->interests, self::INTERESTS),
        );
    }
}
