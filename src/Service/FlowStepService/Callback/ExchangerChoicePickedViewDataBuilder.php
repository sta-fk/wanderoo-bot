<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\ExchangeCountryInputViewData;
use App\DTO\Internal\ExchangePickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
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
        return $update->supportsCallbackQuery(CallbackQueryData::ExchangeChoice);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForExchangeChoicePicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $choice = $update->getCustomCallbackQueryData(CallbackQueryData::ExchangeChoice);

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
