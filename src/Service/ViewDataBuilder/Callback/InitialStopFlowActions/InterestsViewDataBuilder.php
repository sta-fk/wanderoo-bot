<?php

namespace App\Service\ViewDataBuilder\Callback\InitialStopFlowActions;

use App\DTO\Context\PlanContext;
use App\DTO\Internal\InitialStopFlowViewData\BudgetViewData;
use App\DTO\Internal\AddStopFlowViewData\CurrencyChoiceViewData;
use App\DTO\Internal\AddStopFlowViewData\CustomBudgetInputViewData;
use App\DTO\Internal\InitialStopFlowViewData\InterestsViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\UserStateStorage;

readonly class InterestsViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private CurrencyExchangerService $currencyExchangerService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::Interest)
            || $update->supportsCallbackQuery(CallbackQueryData::InterestsDone);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForInterests];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $callbackData = $update->callbackQuery->data;

        if (CallbackQueryData::InterestsDone->value === $callbackData && $context->isAddingStopFlow) {
            return $this->processInterestsDoneForAddingStop($update, $context);
        }

        if (CallbackQueryData::InterestsDone->value === $callbackData) {
            return $this->processInterestsDoneForInitialStop($update, $context);
        }

        $this->processSelectedInterests($update, $context);

        return ViewDataCollection::createWithSingleViewData(
            new InterestsViewData(
                chatId: $chatId,
                messageId: $update->getCallbackMessageId(),
                selectedInterests: $context->currentStopDraft->interests,
            )
        );
    }

    private function processInterestsDoneForAddingStop(TelegramUpdate $update, PlanContext $context): ViewDataCollection
    {
        $processedViewData = new InterestsViewData(
            chatId: $update->getChatId(),
            messageId: $update->getCallbackMessageId(),
            selectedInterests: $context->currentStopDraft->getInterestsLabels(),
            interestsDone: true
        );

        [$nextViewData, $nextState] =
            $context->currentStopDraft->currency !== $context->currency
            && false === $context->isSetDefaultCurrency
                ? [new CurrencyChoiceViewData($update->getChatId()), States::WaitingForCurrencyChoicePicked]
                : $this->buildCustomBudgetInputViewData($update->getChatId(), $context->currency);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add($processedViewData)
            ->add($nextViewData)
            ->setNextState($nextState);

        return $viewDataCollection;
    }

    private function buildCustomBudgetInputViewData(int $chatId, string $globalCurrency): array
    {
        $potentialAmount = $this->currencyExchangerService->convert(100, CallbackQueryData::Usd->value, $globalCurrency);

        return [
            new CustomBudgetInputViewData($chatId, $globalCurrency, $potentialAmount),
            States::WaitingForCustomBudgetInput
        ];
    }

    private function processInterestsDoneForInitialStop(TelegramUpdate $update, PlanContext $context): ViewDataCollection
    {
        $processedViewData = new InterestsViewData(
            chatId: $update->getChatId(),
            messageId: $update->getCallbackMessageId(),
            selectedInterests: $context->currentStopDraft->getInterestsLabels(),
            interestsDone: true
        );

        // $context->currency - Основна валюта для першого кроку, надалі буде CustomBudget
        $nextViewData = new BudgetViewData($update->getChatId(), $context->currency);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add($processedViewData)
            ->add($nextViewData)
            ->setNextState(States::WaitingForBudget);

        return $viewDataCollection;
    }

    private function processSelectedInterests(TelegramUpdate $update, PlanContext $context): void
    {
        $selectedInterest = $update->getCustomCallbackQueryData(CallbackQueryData::Interest);
        if (!in_array($selectedInterest, $context->currentStopDraft->interests ?? [], true)) {
            $context->currentStopDraft->interests[] = $selectedInterest;
        } else {
            $context->currentStopDraft->interests = array_filter(
                $context->currentStopDraft->interests,
                static fn ($interest) => $interest !== $selectedInterest
            );
        }

        $this->userStateStorage->saveContext($update->getChatId(), $context);
    }
}
