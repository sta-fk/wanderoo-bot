<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepServiceInterface;

readonly class StartNoService implements FlowStepServiceInterface
{
    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::StartNo->value === $update->callbackQuery->data
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        return new SendMessageContext(
            $update->callbackQuery->message->chat->id,
            "Натисни “🧳 Так”, щоб почати планування ✈️",
            null,
            States::WaitingForStart
        );
    }
}
