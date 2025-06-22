<?php

namespace App\Service\FlowViewer\Callback\AddStopFlowActions;

use App\DTO\Internal\CurrencyPickedViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\CurrencyResolverService;
use App\Service\FlowViewer\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CurrencyCountryPickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlaceServiceInterface $placeService,
        private CurrencyResolverService $currencyResolverService,
        private CurrencyExchangerService $currencyExchangerService,
        private BudgetHelperService $budgetHelperService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::CurrencyCountryPick);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCurrencyPicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $countryDetails = $this->placeService->getPlaceDetails(
            $update->getCustomCallbackQueryData(CallbackQueryData::CurrencyCountryPick)
        );

        $currency = $this->currencyResolverService->resolveCurrencyCode($countryDetails->countryCode);

        // !! Встановити нову основну валюту
        $this->budgetHelperService->recalculateAllStopBudgetsToNewCurrency($context, $currency);
        $this->userStateStorage->saveContext($chatId, $context);

        $potentialAmount = $this->currencyExchangerService->convert(500, CallbackQueryData::Usd->value, $context->currency);
        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add(new CurrencyPickedViewData($update->callbackQuery->id, $context->currency))
            ->add(new CustomBudgetInputViewData($chatId, $context->currency, $potentialAmount))
            ->setNextState(States::WaitingForCustomBudgetInput);

        return $viewDataCollection;
    }
}
