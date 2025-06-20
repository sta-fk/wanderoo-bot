<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\BudgetProcessedViewData;
use App\DTO\Internal\CityInputSearchResultViewData;
use App\DTO\Internal\CountryInputSearchResultViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\CustomDurationInputViewData;
use App\DTO\Internal\DurationProcessedViewData;
use App\DTO\Internal\CustomDurationValidationFailedViewData;
use App\DTO\Internal\ReuseOrNewTripStyleViewData;
use App\DTO\Internal\StartDateViewData;
use App\DTO\Internal\StartNewViewData;
use App\DTO\Internal\TripStopCreationFinishedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\BudgetHelperService;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;
use Doctrine\DBAL\Schema\View;

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
        return null !== $update->message;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCustomBudgetInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->message->chat->id;
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
        $viewDataCollection->add(new BudgetProcessedViewData($chatId, ($context->stops[count($context->stops) - 1])->budgetInPlanCurrency, $context->currency));
        $viewDataCollection->add(new TripStopCreationFinishedViewData($chatId));
        $viewDataCollection->setNextState(States::TripStopCreationFinished);

        return $viewDataCollection;
    }
}
