<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Enum\TelegramButtons;

readonly class StartNoService implements FlowStepServiceInterface
{
    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && TelegramButtons::StartNo->value === $update->callbackQuery->data;
    }

    public function getNextState(): States
    {
        return States::WaitingForStart;
    }

    public function buildMessage(TelegramUpdate $update): SendMessageContext
    {
        return new SendMessageContext($update->message->chat->id, 'Натисни “🧳 Так”, щоб почати планування ✈️');
    }
}
