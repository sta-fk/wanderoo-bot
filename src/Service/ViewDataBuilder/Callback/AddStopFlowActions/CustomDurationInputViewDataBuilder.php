<?php

namespace App\Service\ViewDataBuilder\Callback\AddStopFlowActions;

use App\DTO\Internal\AddStopFlowViewData\CustomDurationInputViewData;
use App\DTO\Internal\InitialStopFlowViewData\DurationProcessedViewData;
use App\DTO\Internal\AddStopFlowViewData\ReuseOrNewTripStyleViewData;
use App\DTO\Internal\InitialStopFlowViewData\StartDateViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class CustomDurationInputViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private TranslatorInterface $translator,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->isMessageUpdate();
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCustomDurationInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        if (!is_numeric($update->message->text) || $update->message->text < 0 || $update->message->text >= 30) {
            return $this->getValidationMessageViewData($chatId);
        }

        $context->currentStopDraft->duration = (int)$update->message->text;
        $this->userStateStorage->saveContext($chatId, $context);

        [$nextViewData, $nextState] = [new StartDateViewData($chatId), States::WaitingForStartDate];

        if ($context->isAddingStopFlow) {
            [$nextViewData, $nextState] = [
                new ReuseOrNewTripStyleViewData($chatId, $context->getLastSavedStop()->getTripStyleLabel()),
                States::WaitingForReuseOrNewTripStyle
            ];
        }

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add(new DurationProcessedViewData($chatId, $context->currentStopDraft->duration))
            ->add($nextViewData)
            ->setNextState($nextState)
        ;

        return $viewDataCollection;
    }

    private function getValidationMessageViewData(int $chatId): ViewDataCollection
    {
        return ViewDataCollection::createWithSingleViewData(
            new CustomDurationInputViewData(
                $chatId,
                $this->translator->trans('trip.context.custom_duration.invalid_value')
            )
        );
    }
}
