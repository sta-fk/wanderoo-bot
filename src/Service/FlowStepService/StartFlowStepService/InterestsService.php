<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\KeyboardService\BuildBudgetKeyboardTrait;
use App\Service\KeyboardService\BuildInterestsKeyboardTrait;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class InterestsService implements StateAwareFlowStepServiceInterface
{
    use BuildBudgetKeyboardTrait;
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
        return null !== $update->callbackQuery
            && (str_starts_with($update->callbackQuery->data, CallbackQueryData::Interest->value)
            || $update->callbackQuery->data === CallbackQueryData::InterestsDone->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForInterests];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {

        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $callbackData = $update->callbackQuery->data;

        if (CallbackQueryData::InterestsDone->value === $callbackData) {
            $selectedLabels = array_map(
                static fn ($key) => strtolower(self::INTERESTS[$key]) ?? $key,
                $context->currentStopDraft->interests ?? []
            );

            if ($context->isAddingStopFlow) {
                return new SendMessageContext(
                    $chatId,
                    "Чудово! Ви обрали інтереси: " . implode(', ', $selectedLabels) . ".\n\n✍️ Введіть бажаний бюджет у євро (наприклад: <b>100</b>):",
                    null,
                    States::WaitingForCustomBudget
                );
            }

            $keyboard = $this->buildBudgetKeyboard(BudgetService::BUDGET_OPTIONS);

            return new SendMessageContext(
                $chatId,
                "Чудово! Ви обрали інтереси: " . implode(', ', $selectedLabels) . ".\n\n💰 Тепер оберіть орієнтовний бюджет на подорож:",
                $keyboard,
                States::WaitingForBudget
            );
        }

        $selectedInterest = substr($callbackData, strlen(CallbackQueryData::Interest->value));
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
                $context->currentStopDraft->interests,
                self::INTERESTS
            ),
        );
    }
}
