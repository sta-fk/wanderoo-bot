<?php

namespace App\Service\FlowViewer\Callback\TripStopCreationFinished;

use App\DTO\Internal\TripStopGenerationFinishedViewData\DraftPlanCurrencyCountryInputViewData;
use App\DTO\Internal\TripStopGenerationFinishedViewData\DraftPlanCurrencyPickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\FlowViewer\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class DraftPlanCurrencyChoicePickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private BudgetHelperService $budgetHelperService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::DraftPlanCurrencyChoice);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForDraftPlanCurrencyChoicePicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $choice = $update->getCustomCallbackQueryData(CallbackQueryData::DraftPlanCurrencyChoice);

        if ($choice === CallbackQueryData::Auto->value) {
            return ViewDataCollection::createStateAwareWithSingleViewData(
                new DraftPlanCurrencyCountryInputViewData($chatId),
                States::WaitingForDraftPlanCurrencyCountryInput
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
        $this->userStateStorage->resetState($chatId);

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
