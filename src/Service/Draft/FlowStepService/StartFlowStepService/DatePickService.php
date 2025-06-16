<?php

namespace App\Service\Draft\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\FlowStepService\StateAwareFlowViewDataServiceInterface;
use App\Service\Draft\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

class DatePickService implements StateAwareFlowViewDataServiceInterface
{
    public function __construct(
        private readonly UserStateStorage $userStateStorage,
        private readonly NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::PickDate->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStartDate];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $dateStr = substr($update->callbackQuery->data, strlen(CallbackQueryData::PickDate->value)); // YYYY-MM-DD
        $chatId = $update->callbackQuery->message->chat->id;

        $context = $this->userStateStorage->getContext($chatId);
        if (null === $context->currentStopDraft->duration) {
            throw new \RuntimeException("Invalid payload");
        }

        $context->startDate = (new \DateTimeImmutable($dateStr));

        $endDate = (new \DateTimeImmutable($dateStr))->modify("+{$context->currentStopDraft->duration} days");
        $context->endDate = $endDate;

        $this->userStateStorage->saveContext($chatId, $context);

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForTripStyle);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage($chatId),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForTripStyle
        );
    }
}
