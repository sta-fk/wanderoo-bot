<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Budget\BudgetHelperService;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class CurrencyChoiceService implements StateAwareFlowStepServiceInterface
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
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::CurrencyChoice->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCurrencyChoice];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $choice = substr($update->callbackQuery->data, strlen(CallbackQueryData::CurrencyChoice->value));

        if ($choice === CallbackQueryData::FromCountry->value) {
            $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCurrencyCountrySearch);

            return new SendMessageContext(
                $chatId,
                $nextStateKeyboardProvider->getTextMessage(),
                $nextStateKeyboardProvider->buildKeyboard(),
                States::WaitingForCurrencyCountrySearch,
            );
        }

        if ($choice === CallbackQueryData::Usd->value || $choice === CallbackQueryData::Eur->value) {
            $context->currency = $choice;
        }

        // !! Встановити нову основну валюту
        $this->budgetHelperService->recalculateAllStopBudgetsToNewCurrency($context, $context->currency);

        $this->userStateStorage->saveContext($chatId, $context);

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCustomBudget);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage($chatId),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForCustomBudget
        );
    }
}
