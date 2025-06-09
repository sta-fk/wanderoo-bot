<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\KeyboardService\BuildCalendarKeyboardTrait;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\UserStateStorage;

class DurationService implements StateAwareFlowStepServiceInterface
{
    use BuildCalendarKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Duration->value)
        ;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForDuration];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $durationValue = substr($update->callbackQuery->data, strlen(CallbackQueryData::Duration->value));
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (CallbackQueryData::Custom->value === $durationValue) {
            return new SendMessageContext(
                $chatId,
                "Введіть кількість днів (наприклад, 4):",
                null,
                States::WaitingForCustomDuration
            );
        }

        $context->currentStopDraft->duration = (int) $durationValue;
        $this->userStateStorage->saveContext($chatId, $context);

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $keyboard = $this->buildCalendarKeyboard($now->format('Y'), $now->format('m'));
        $text = "Чудово! Подорож на {$durationValue} днів. \n\n📅 Тепер оберіть дату подорожі:";

        return new SendMessageContext($chatId, $text, $keyboard, States::WaitingForStartDate);
    }
}
