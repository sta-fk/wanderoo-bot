<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

readonly class DurationService implements FlowStepServiceInterface
{

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, CallbackQueryData::Duration->value);
    }

    public function getNextState(): States
    {
        return States::ReadyForDates;
    }

    public function buildMessage(TelegramUpdate $update): SendMessageContext
    {
        $durationValue = substr($update->callbackQuery->data, 9);
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if ($durationValue === 'custom') {
            $this->userStateStorage->updateState($chatId, States::WaitingForCustomDuration);

            return new SendMessageContext($chatId, "Введіть кількість днів (наприклад, 4):");
        }

        $context->duration = (int) $durationValue;
        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext($chatId, "Чудово! Подорож на {$durationValue} днів. Тепер оберіть дати поїздки.");
    }
}
