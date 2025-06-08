<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\KeyboardService\BuildBudgetKeyboardTrait;
use App\Service\KeyboardService\BuildInterestsKeyboardTrait;
use App\Service\KeyboardService\BuildKeyboardTrait;
use App\Service\FlowStepService\StartFlowStepService\BudgetService;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class StopInterestsService implements StateAwareFlowStepServiceInterface
{
    use BuildInterestsKeyboardTrait;

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
            (str_starts_with($update->callbackQuery->data, CallbackQueryData::StopInterest->value) ||
                $update->callbackQuery->data === CallbackQueryData::StopInterestsDone->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStopInterests];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {

        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $callbackData = $update->callbackQuery->data;

        if (CallbackQueryData::StopInterestsDone->value === $callbackData) {
            $selectedLabels = array_map(
                static fn ($key) => strtolower(self::INTERESTS[$key]) ?? $key,
                $context->currentStopDraft->interests ?? []
            );

            return new SendMessageContext(
                $chatId,
                "Чудово! Ви обрали інтереси: " . implode(', ', $selectedLabels) . ".\n\n✍️ Тепер введіть бажаний бюджет у євро (наприклад: <b>150</b>):",
                null,
                States::WaitingForStopCustomBudget
            );
        }

        $selectedInterest = substr($callbackData, strlen(CallbackQueryData::StopInterest->value));
        if (!in_array($selectedInterest, $context->currentStopDraft->interests ?? [], true)) {
            $context->currentStopDraft->interests[] = $selectedInterest;
        } else {
            $context->currentStopDraft->interests = array_filter(
                $context->currentStopDraft->interests,
                static fn ($interest) => $interest !== $selectedInterest
            );
        }

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "✨ Оновлено. Щось ще?",
            $this->buildInterestsKeyboard(
                CallbackQueryData::StopInterest,
                CallbackQueryData::StopInterestsDone,
                $context->currentStopDraft->interests,
                self::INTERESTS
            ),
        );
    }
}
