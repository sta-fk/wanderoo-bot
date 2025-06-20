<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CurrencyChoiceViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\InterestsViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use App\Service\UserStateStorage;

readonly class ReuseOrNewInterestsViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private CurrencyExchangerService $currencyExchangerService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Interest->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForReuseOrNewInterests];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $action = substr($update->callbackQuery->data, strlen(CallbackQueryData::Interest->value));

        if ($action === CallbackQueryData::Reuse->value) {
            $lastOneStop = ($context->stops[count($context->stops) - 1]);
            $currentStopDraft = $context->currentStopDraft;

            $currentStopDraft->interests = $lastOneStop->interests;
            $this->userStateStorage->saveContext($chatId, $context);

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
                ->setNextState($nextState)
            ;

            return $viewDataCollection;
        }

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new InterestsViewData(chatId: $chatId, cityName: $context->currentStopDraft->cityName),
            States::WaitingForInterests
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
}
