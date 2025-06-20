<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CurrencyPickedViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\ExchangePickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\CurrencyResolverService;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class ExchangeCountryPickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlaceServiceInterface $placeService,
        private CurrencyResolverService $currencyResolverService,
        private BudgetHelperService $budgetHelperService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::ExchangeCountryPick->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForExchangePicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
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

        return ViewDataCollection::createWithSingleViewData(
            new ExchangePickedViewData(
                $chatId,
                $context->totalBudget,
                $context->currency,
                $fromTotalBudget,
                $fromCurrency
            )
        );
    }
}
