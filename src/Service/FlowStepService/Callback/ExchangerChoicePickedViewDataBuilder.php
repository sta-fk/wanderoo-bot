<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CurrencyCountryInputViewData;
use App\DTO\Internal\CurrencyPickedViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\ExchangeCountryInputViewData;
use App\DTO\Internal\ExchangePickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\UserStateStorage;

readonly class ExchangerChoicePickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private BudgetHelperService $budgetHelperService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::ExchangeChoice->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForExchangeChoicePicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $choice = substr($update->callbackQuery->data, strlen(CallbackQueryData::ExchangeChoice->value));

        if ($choice === CallbackQueryData::Auto->value) {
            return ViewDataCollection::createStateAwareWithSingleViewData(
                new ExchangeCountryInputViewData($chatId),
                States::WaitingForExchangeCountryInput
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
