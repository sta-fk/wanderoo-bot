<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetOptionsProvider;
use App\Service\CurrencyResolverService;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardProvider\NextStateKeyboardProviderInterface;
use App\Service\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class InterestsService implements StateAwareFlowStepServiceInterface
{
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
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
        private CurrencyResolverService $currencyResolverService,
        private BudgetOptionsProvider $budgetOptionsProvider,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && (str_starts_with($update->callbackQuery->data, CallbackQueryData::Interest->value)
            || $update->callbackQuery->data === CallbackQueryData::InterestsDone->value)
        ;
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

        if (CallbackQueryData::InterestsDone->value === $callbackData && $context->isAddingStopFlow) {
            $contextCurrencyCode = $this->currencyResolverService->resolveCurrencyCode($context->currentStopDraft->countryCode);
            $nextState =  $contextCurrencyCode !== $context->currency ? States::WaitingForCurrencyChoice : States::WaitingForCustomBudget;

            return $this->buildAddingStopMessageContext($chatId, $nextState, $this->keyboardProviderResolver->resolve($nextState));
        }

        if (CallbackQueryData::InterestsDone->value === $callbackData) {
            $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForBudget);

            return $this->buildInterestsDoneMessageContext($chatId, $context, $nextStateKeyboardProvider);
        }

        $this->processSelectedInterests($update, $context);

        $this->userStateStorage->saveContext($chatId, $context);

        return $this->buildContinueSelectingInterestsMessageContext($chatId, $context);
    }

    private function buildAddingStopMessageContext(int $chatId, States $nextState, NextStateKeyboardProviderInterface $keyboardProvider): SendMessageContext
    {
        return new SendMessageContext(
            $chatId,
            $keyboardProvider->getTextMessage($chatId),
            $keyboardProvider->buildKeyboard(),
            $nextState
        );
    }

    private function buildInterestsDoneMessageContext(int $chatId, PlanContext $context, NextStateKeyboardProviderInterface $keyboardProvider): SendMessageContext
    {
        // $context->currency - Основна валюта для першого кроку, надалі буде CustomBudget
        $keyboard = $keyboardProvider->buildKeyboard(
            $this->budgetOptionsProvider->getBudgetOptionsInCurrency($context->currency)
        );

        return new SendMessageContext(
            $chatId,
            $keyboardProvider->getTextMessage($chatId),
            $keyboard,
            States::WaitingForBudget
        );
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

    private function buildContinueSelectingInterestsMessageContext(int $chatId, PlanContext $context): SendMessageContext
    {
        return new SendMessageContext(
            $chatId,
            "✨ Оновлено. Щось ще?",
            $this->keyboardProviderResolver
                ->resolve(States::WaitingForInterests)
                ->buildKeyboard($context->currentStopDraft->interests)
        );
    }
}
