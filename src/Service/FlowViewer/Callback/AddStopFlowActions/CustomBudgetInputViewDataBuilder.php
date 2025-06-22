<?php

namespace App\Service\FlowViewer\Callback\AddStopFlowActions;

use App\DTO\Internal\BudgetProcessedViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\TripStopCreationFinishedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\FlowViewer\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\UserStateStorage;

readonly class CustomBudgetInputViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private BudgetHelperService $budgetHelper,
        private CurrencyExchangerService $currencyExchangerService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->isMessageUpdate();
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCustomBudgetInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $userInput = preg_replace('/[^\d]/', '', $update->message->text);

        if (!is_numeric($userInput)) {
            $potentialAmount = $this->currencyExchangerService->convert(500, CallbackQueryData::Usd->value, $context->currency);
            return ViewDataCollection::createWithSingleViewData(
                new CustomBudgetInputViewData($chatId, $context->currency, $potentialAmount, false)
            );
        }

        // ⬇️ Конвертація бюджету в валюту плану
        $this->budgetHelper->applyBudgetToStop($context->currentStopDraft, $context, $userInput);

        $context->finishCreatingNewStop();
        $this->userStateStorage->saveContext($chatId, $context);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add(new BudgetProcessedViewData($chatId, $context->getLastSavedStop()->budgetInPlanCurrency, $context->currency))
            ->add(new TripStopCreationFinishedViewData($chatId))
            ->setNextState(States::TripStopCreationFinished);

        return $viewDataCollection;
    }
}
