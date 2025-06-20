<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CurrencyCountryInputViewData;
use App\DTO\Internal\CurrencyPickedViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
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
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::CurrencyChoice->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCurrencyChoicePicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $choice = substr($update->callbackQuery->data, strlen(CallbackQueryData::CurrencyChoice->value));

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
        $viewDataCollection->add(new CurrencyPickedViewData($update->callbackQuery->id, $context->currency));
        $viewDataCollection->add(new CustomBudgetInputViewData($chatId, $context->currency, $potentialAmount));
        $viewDataCollection->setNextState(States::WaitingForCustomBudgetInput);

        return $viewDataCollection;
    }
}
