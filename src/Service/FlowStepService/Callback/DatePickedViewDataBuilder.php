<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\DatePickedViewData;
use App\DTO\Internal\TripStyleViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class DatePickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::DatePicked->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStartDate];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $dateStr = substr($update->callbackQuery->data, strlen(CallbackQueryData::DatePicked->value)); // YYYY-MM-DD
        $chatId = $update->callbackQuery->message->chat->id;

        $context = $this->userStateStorage->getContext($chatId);
        if (null === $context->currentStopDraft->duration) {
            throw new \RuntimeException("Invalid payload");
        }

        $context->startDate = (new \DateTimeImmutable($dateStr));

        $endDate = (new \DateTimeImmutable($dateStr))->modify("+{$context->currentStopDraft->duration} days");
        $context->endDate = $endDate;

        $this->userStateStorage->saveContext($chatId, $context);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection->add(new DatePickedViewData($chatId, $update->callbackQuery->id, $context->startDate, $context->endDate));
        $viewDataCollection->add(new TripStyleViewData($chatId));
        $viewDataCollection->setNextState(States::WaitingForTripStyle);

        return $viewDataCollection;
    }
}
