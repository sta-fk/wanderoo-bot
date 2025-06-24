<?php

namespace App\Service\ViewDataBuilder\Callback\AddStopFlowActions;

use App\DTO\Internal\AddStopFlowViewData\CurrencyCountryInputViewData;
use App\DTO\Internal\AddStopFlowViewData\CurrencyPickedViewData;
use App\DTO\Internal\AddStopFlowViewData\CustomBudgetInputViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\UserStateStorage;

readonly class CurrencyChoicePickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private BudgetHelperService $budgetHelperService,
        private CurrencyExchangerService $currencyExchangerService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::CurrencyChoice);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCurrencyChoicePicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $choice = $update->getCustomCallbackQueryData(CallbackQueryData::CurrencyChoice);

        if ($choice === CallbackQueryData::Auto->value) {
            return ViewDataCollection::createStateAwareWithSingleViewData(
                new CurrencyCountryInputViewData($chatId),
                States::WaitingForCurrencyCountryInput
            );
        }

        if ($choice === CallbackQueryData::Usd->value || $choice === CallbackQueryData::Eur->value) {
            $context->currency = $choice;
        }

        // !! Встановити нову основну валюту
        $this->budgetHelperService->recalculateAllStopBudgetsToNewCurrency($context, $context->currency);

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
