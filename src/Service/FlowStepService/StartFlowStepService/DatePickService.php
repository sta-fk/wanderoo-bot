<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\GetTripStyleKeyboardTrait;
use App\Service\UserStateStorage;

class DatePickService implements StateAwareFlowStepServiceInterface
{
    use GetTripStyleKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, CallbackQueryData::PickDate->value);
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

        $keyboard = $this->getTripStyleKeyboard();
        $text = "✅ Подорож з <b>$dateStr</b> по <b>{$endDate->format('Y-m-d')}</b> \n\nЯкий стиль подорожі ви бажаєте? 🧳";

        return new SendMessageContext($chatId, $text, $keyboard, States::WaitingForTripStyle);
    }
}
