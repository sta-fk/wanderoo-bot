<?php

namespace App\Service\Draft\FlowStepService\CurrencyConversion;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\Budget\BudgetHelperService;
use App\Service\Draft\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\Draft\KeyboardProvider\NextState\ExchangeDoneKeyboardProvider;
use App\Service\Draft\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class ExchangeChoiceService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
        private BudgetHelperService $budgetHelperService,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::ExchangeChoice->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForExchangeChoice];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $choice = substr($update->callbackQuery->data, strlen(CallbackQueryData::ExchangeChoice->value));

        if (CallbackQueryData::FromCountry->value === $choice) {
            $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForExchangeCountrySearch);

            return new SendMessageContext(
                $chatId,
                $nextStateKeyboardProvider->getTextMessage(),
                $nextStateKeyboardProvider->buildKeyboard(),
                States::WaitingForExchangeCountrySearch,
            );
        }

        if ($choice !== CallbackQueryData::Usd->value && $choice !== CallbackQueryData::Eur->value) {
            throw new \LogicException('Unavailable currency conversion step');
        }

        $fromCurrency = $context->currency;
        $fromTotalBudget = $context->totalBudget;
        $context->currency = $choice;

        // !! Встановити нову основну валюту
        $this->budgetHelperService->recalculateAllStopBudgetsToNewCurrency($context, $context->currency);

        $this->userStateStorage->saveContext($chatId, $context);

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::ExchangeDone);

        if (!($nextStateKeyboardProvider instanceof ExchangeDoneKeyboardProvider)) {
            throw new \LogicException('Unavailable currency conversion step');
        }

        $textMessage =  $nextStateKeyboardProvider
            ->setCurrencyConversion($fromTotalBudget, $fromCurrency, $context->totalBudget, $context->currency)
            ->getTextMessage();

        return new SendMessageContext(
            $chatId,
            $textMessage,
            $nextStateKeyboardProvider->buildKeyboard(),
        );
    }
}
