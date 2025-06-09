<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\KeyboardService\BuildInterestsKeyboardTrait;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class InterestsService implements StateAwareFlowStepServiceInterface
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
                return $this->buildAddingStopMessageContext($chatId, $selectedLabels);
            }

            return $this->buildInterestsDoneMessageContext($chatId, $selectedLabels);
        }

        $this->processSelectedInterests($update, $context);

        $this->userStateStorage->saveContext($chatId, $context);

        $keyboard = $this->buildInterestsKeyboard($context->currentStopDraft->interests, self::INTERESTS);

        return new SendMessageContext($chatId, "✨ Оновлено. Щось ще?", $keyboard);
    }

    private function buildAddingStopMessageContext(int $chatId, array $selectedInterestsLabels): SendMessageContext
    {
        return new SendMessageContext(
            $chatId,
            "Чудово! Ви обрали інтереси: " . implode(', ', $selectedInterestsLabels) . ".\n\n✍️ Введіть бажаний бюджет у євро (наприклад: <b>100</b>):",
            null,
            States::WaitingForCustomBudget
        );
    }

    private function buildInterestsDoneMessageContext(int $chatId, array $selectedInterestsLabels): SendMessageContext
    {
        return new SendMessageContext(
            $chatId,
            "Чудово! Ви обрали інтереси: " . implode(', ', $selectedInterestsLabels) . ".\n\n💰 Тепер оберіть орієнтовний бюджет на подорож:",
            $this->buildBudgetKeyboard(BudgetService::BUDGET_OPTIONS),
            States::WaitingForBudget
        );
    }

    private function buildBudgetKeyboard(array $budgetOptions): array
    {
        $budgetKeyboard = [];
        foreach ($budgetOptions as $callback => $label) {
            $budgetKeyboard[] = [[
                'text' => $label,
                'callback_data' => CallbackQueryData::Budget->value . $callback,
            ]];
        }

        return ['inline_keyboard' => $budgetKeyboard];
    }

    private function processSelectedInterests(TelegramUpdate $update, PlanContext $context): void
    {
        $callbackData = $update->callbackQuery->data;

        $selectedInterest = substr($callbackData, strlen(CallbackQueryData::Interest->value));
        if (!in_array($selectedInterest, $context->currentStopDraft->interests ?? [], true)) {
            $context->currentStopDraft->interests[] = $selectedInterest;
        } else {
            $context->currentStopDraft->interests = array_filter(
                $context->currentStopDraft->interests,
                static fn ($interest) => $interest !== $selectedInterest
            );
        }
    }
}
