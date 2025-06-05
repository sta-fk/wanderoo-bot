<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

readonly class CustomDurationService implements FlowStepServiceInterface
{

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && States::WaitingForCustomDuration === $this->userStateStorage->getState($update->callbackQuery->message->chat->id);
    }

    public function getNextState(): States
    {
        return States::ReadyForDates;
    }

    public function buildMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (null !== $update->message->text && (int)$update->message->text > 0 && (int)$update->message->text <= 30) {
            $context->duration = (int) $update->message->text;
            $this->userStateStorage->saveContext($chatId, $context);

            return new SendMessageContext($chatId, "Чудово! Подорож на {$update->message->text} днів. Тепер оберіть дати поїздки.");
        }

        return new SendMessageContext($chatId, "Будь ласка, введіть число від 1 до 30.");
    }
}
