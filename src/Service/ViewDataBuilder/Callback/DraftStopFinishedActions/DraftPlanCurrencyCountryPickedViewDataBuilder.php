<?php

namespace App\Service\ViewDataBuilder\Callback\DraftStopFinishedActions;

use App\DTO\Internal\TripStopGenerationFinishedViewData\DraftPlanCurrencyPickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\CurrencyResolverService;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class DraftPlanCurrencyCountryPickedViewDataBuilder implements StateAwareViewDataBuilderInterface
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
        return $update->supportsCallbackQuery(CallbackQueryData::DraftPlanCurrencyCountryPick);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForDraftPlanCurrencyPicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $countryDetails = $this->placeService->getPlaceDetails(
            $update->getCustomCallbackQueryData(CallbackQueryData::DraftPlanCurrencyCountryPick)
        );

        $fromCurrency = $context->currency;
        $fromTotalBudget = $context->totalBudget;
        $context->currency = $this->currencyResolverService->resolveCurrencyCode($countryDetails->countryCode);

        // !! Встановити нову основну валюту
        $this->budgetHelperService->recalculateAllStopBudgetsToNewCurrency($context, $context->currency);

        $this->userStateStorage->saveContext($chatId, $context);

        return ViewDataCollection::createWithSingleViewData(
            new DraftPlanCurrencyPickedViewData(
                $chatId,
                $context->totalBudget,
                $context->currency,
                $fromTotalBudget,
                $fromCurrency
            )
        );
    }
}
