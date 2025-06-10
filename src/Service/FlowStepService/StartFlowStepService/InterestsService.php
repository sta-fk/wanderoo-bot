<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
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
            return $this->buildInterestsDoneMessageContext($chatId, $this->keyboardProviderResolver->resolve(States::WaitingForBudget));
        }

        $this->processSelectedInterests($update, $context);

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "✨ Оновлено. Щось ще?",
            $this->keyboardProviderResolver
                ->resolve(States::WaitingForInterests)
                ->buildKeyboard($context->currentStopDraft->interests)
        );
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

    private function buildInterestsDoneMessageContext(int $chatId, NextStateKeyboardProviderInterface $keyboardProvider): SendMessageContext
    {
        return new SendMessageContext(
            $chatId,
            $keyboardProvider->getTextMessage($chatId),
            $keyboardProvider->buildKeyboard(),
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
}
