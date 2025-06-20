<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Context\PlanContext;
use App\DTO\Internal\BudgetViewData;
use App\DTO\Internal\CurrencyChoiceViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\InterestsViewData;
use App\DTO\Internal\ReuseOrNewInterestsViewData;
use App\DTO\Internal\TripStylePickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\UserStateStorage;
use Doctrine\DBAL\Schema\View;

readonly class InterestsViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private CurrencyExchangerService $currencyExchangerService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && (str_starts_with($update->callbackQuery->data, CallbackQueryData::Interest->value)
                || $update->callbackQuery->data === CallbackQueryData::InterestsDone->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForInterests];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $callbackData = $update->callbackQuery->data;

        if (CallbackQueryData::InterestsDone->value === $callbackData && $context->isAddingStopFlow) {
            $processedViewData = new InterestsViewData(
                chatId: $chatId,
                messageId: $update->callbackQuery->message->messageId,
                selectedInterests: $context->currentStopDraft->getInterestsLabels(),
                interestsDone: true
            );

            [$nextViewData, $nextState] =
                $context->currentStopDraft->currency !== $context->currency
                && false === $context->isSetDefaultCurrency
                    ? [new CurrencyChoiceViewData($chatId), States::WaitingForCurrencyChoicePicked]
                    : $this->buildCustomBudgetInputViewData($chatId, $context->currency);

            $viewDataCollection = new ViewDataCollection();
            $viewDataCollection
                ->add($processedViewData)
                ->add($nextViewData)
                ->setNextState($nextState);

            return $viewDataCollection;
        }

        if (CallbackQueryData::InterestsDone->value === $callbackData) {
            $processedViewData = new InterestsViewData(chatId: $chatId, messageId: $update->callbackQuery->message->messageId, selectedInterests: $context->currentStopDraft->getInterestsLabels(), interestsDone: true);
            $nextViewData = new BudgetViewData($chatId, $context->currency); // $context->currency - Основна валюта для першого кроку, надалі буде CustomBudget

            $viewDataCollection = new ViewDataCollection();
            $viewDataCollection
                ->add($processedViewData)
                ->add($nextViewData)
                ->setNextState(States::WaitingForBudget);

            return $viewDataCollection;
        }

        $this->processSelectedInterests($update, $context);

        return ViewDataCollection::createWithSingleViewData(
            new InterestsViewData(
                chatId: $chatId,
                messageId: $update->callbackQuery->message->messageId,
                selectedInterests: $context->currentStopDraft->interests,
            )
        );
    }

    private function buildCustomBudgetInputViewData(int $chatId, string $globalCurrency): array
    {
        $potentialAmount = $this->currencyExchangerService->convert(100, CallbackQueryData::Usd->value, $globalCurrency);

        return [
            new CustomBudgetInputViewData($chatId, $globalCurrency, $potentialAmount),
            States::WaitingForCustomBudgetInput
        ];
    }

    private function processSelectedInterests(TelegramUpdate $update, PlanContext $context): void
    {
        $callbackData = $update->callbackQuery->data;

        $selectedInterest = substr($callbackData, strlen(CallbackQueryData::Interest->value));
        if (!in_array($selectedInterest, $context->currentStopDraft->interests ?? [], true)) {
            $context->currentStopDraft->interests[] = $selectedInterest;
        } else {
            $context->currentStopDraft->interests = array_filter(
                $context->currentStopDraft->interests,
                static fn ($interest) => $interest !== $selectedInterest
            );
        }

        $this->userStateStorage->saveContext($update->callbackQuery->message->chat->id, $context);
    }
}
