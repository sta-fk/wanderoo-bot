<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\BudgetProcessedViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\TripStopCreationFinishedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\UserStateStorage;

readonly class BudgetViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private BudgetHelperService $budgetHelper,
        private CurrencyExchangerService $currencyExchangerService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::Budget);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForBudget];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $budgetKey = $update->getCustomCallbackQueryData(CallbackQueryData::Budget);

        if (CallbackQueryData::Custom->value === $budgetKey) {
            $potentialAmount = $this->currencyExchangerService->convert(500, CallbackQueryData::Usd->value, $context->currency);

            return ViewDataCollection::createStateAwareWithSingleViewData(
                new CustomBudgetInputViewData($chatId, $context->currency, $potentialAmount),
                States::WaitingForCustomBudgetInput
            );
        }

        $context->currentStopDraft->budget = $budgetKey;

        // ⬇️ NEW: розрахунок бюджету в валюті загального плану
        $range = $this->budgetHelper->resolveBudgetRange($budgetKey, $context->currency);
        if ($range !== null) {
            [$minBudget, $maxBudget] = $range;
            $avgBudget = $maxBudget ? ($minBudget + $maxBudget) / 2 : $minBudget;

            $this->budgetHelper->applyBudgetToStop($context->currentStopDraft, $context, $avgBudget);
        }

        $context->finishCreatingNewStop();
        $this->userStateStorage->saveContext($chatId, $context);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection->add(new BudgetProcessedViewData($chatId, $context->getLastSavedStop()->budgetInPlanCurrency, $context->currency));
        $viewDataCollection->add(new TripStopCreationFinishedViewData($chatId));
        $viewDataCollection->setNextState(States::TripStopCreationFinished);

        return $viewDataCollection;
    }
}
