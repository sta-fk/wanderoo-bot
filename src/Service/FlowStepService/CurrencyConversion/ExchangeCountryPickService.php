<?php

namespace App\Service\FlowStepService\CurrencyConversion;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\CurrencyResolverService;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardProvider\NextState\ExchangeDoneKeyboardProvider;
use App\Service\NextStateKeyboardProviderResolver;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class ExchangeCountryPickService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
        private CurrencyResolverService $currencyResolverService,
        private BudgetHelperService $budgetHelperService,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::ExchangeCountryPick->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForExchangeCountryPick];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $countryPlaceId = substr($update->callbackQuery->data, strlen(CallbackQueryData::ExchangeCountryPick->value));
        $countryDetails = $this->placeService->getPlaceDetails($countryPlaceId);

        $fromCurrency = $context->currency;
        $fromTotalBudget = $context->totalBudget;
        $context->currency = $this->currencyResolverService->resolveCurrencyCode($countryDetails->countryCode);

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
