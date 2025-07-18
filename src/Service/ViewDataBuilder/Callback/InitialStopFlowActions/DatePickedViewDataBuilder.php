<?php

namespace App\Service\ViewDataBuilder\Callback\InitialStopFlowActions;

use App\DTO\Internal\InitialStopFlowViewData\DatePickedViewData;
use App\DTO\Internal\InitialStopFlowViewData\TripStyleViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class DatePickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::DatePicked);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStartDate];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $dateStr = $update->getCustomCallbackQueryData(CallbackQueryData::DatePicked); // YYYY-MM-DD
        $chatId = $update->getChatId();

        $context = $this->userStateStorage->getContext($chatId);
        if (null === $context->currentStopDraft->duration) {
            throw new \RuntimeException("Invalid payload");
        }

        $context->startDate = (new \DateTimeImmutable($dateStr));
        $context->endDate =
            (new \DateTimeImmutable($dateStr))
            ->modify("+{$context->currentStopDraft->duration} days");

        $this->userStateStorage->saveContext($chatId, $context);

        $previousViewData = new DatePickedViewData(
            $chatId,
            $update->callbackQuery->id,
            $context->startDate,
            $context->endDate
        );

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add($previousViewData)
            ->add(new TripStyleViewData($chatId))
            ->setNextState(States::WaitingForTripStyle);

        return $viewDataCollection;
    }
}
