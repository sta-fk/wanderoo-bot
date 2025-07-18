<?php

namespace App\Service\ViewDataBuilder\Callback\InitialStopFlowActions;

use App\DTO\Internal\AddStopFlowViewData\CustomDurationInputViewData;
use App\DTO\Internal\InitialStopFlowViewData\DurationProcessedViewData;
use App\DTO\Internal\InitialStopFlowViewData\StartDateViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class DurationViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private TranslatorInterface $translator,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::Duration);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForDurationPicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $durationValue = $update->getCustomCallbackQueryData(CallbackQueryData::Duration);
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        if (CallbackQueryData::Custom->value === $durationValue) {
            return ViewDataCollection::createStateAwareWithSingleViewData(
                new CustomDurationInputViewData($chatId),
                States::WaitingForCustomDurationInput,
            );
        }

        $context->currentStopDraft->duration = (int) $durationValue;
        $this->userStateStorage->saveContext($chatId, $context);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add(new DurationProcessedViewData($chatId, $context->currentStopDraft->duration))
            ->add(new StartDateViewData($chatId))
            ->setNextState(States::WaitingForStartDate);

        return $viewDataCollection;
    }
}
