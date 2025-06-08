<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\KeyboardService\BuildCalendarKeyboardTrait;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\GetTripStyleKeyboardTrait;
use App\Service\UserStateStorage;

class StopDurationService implements StateAwareFlowStepServiceInterface
{
    use GetTripStyleKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, CallbackQueryData::StopDuration->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStopDuration];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $durationValue = substr($update->callbackQuery->data, strlen(CallbackQueryData::StopDuration->value));
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if ('custom' === $durationValue) {
            return new SendMessageContext(
                $chatId,
                "Введіть кількість днів (наприклад, 4):",
                null,
                States::WaitingForStopCustomDuration
            );
        }

        $context->currentStopDraft->duration = (int) $durationValue;
        $this->userStateStorage->saveContext($chatId, $context);

        $text = "Чудово! Подорож на {$durationValue} днів. Тепер оберіть дати поїздки. \n\n📅 Оберіть стиль подорожі для цієї зупинки:";

        return new SendMessageContext($chatId, $text, $this->getTripStyleKeyboard(CallbackQueryData::StopTripStyle), States::WaitingForStopTripStyle);
    }
}
