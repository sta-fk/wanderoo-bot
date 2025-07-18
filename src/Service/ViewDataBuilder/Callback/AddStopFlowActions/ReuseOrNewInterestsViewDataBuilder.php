<?php

namespace App\Service\ViewDataBuilder\Callback\AddStopFlowActions;

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

readonly class ReuseOrNewInterestsViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private CurrencyExchangerService $currencyExchangerService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::Interest);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForReuseOrNewInterests];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $action = $update->getCustomCallbackQueryData(CallbackQueryData::Interest);

        if ($action === CallbackQueryData::Reuse->value) {
            $currentStopDraft = $context->currentStopDraft;

            $currentStopDraft->interests = $context->getLastSavedStop()->interests;
            $this->userStateStorage->saveContext($chatId, $context);

            $processedViewData = new InterestsViewData(
                chatId: $chatId,
                messageId: $update->getCallbackMessageId(),
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
