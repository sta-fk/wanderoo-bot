<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepServiceInterface;
use App\Service\UserStateStorage;

class CalendarService implements FlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, CallbackQueryData::Calendar->value);
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        [$year, $month] = explode('_', substr($update->callbackQuery->data, strlen(CallbackQueryData::Calendar->value)));

        $keyboard = $this->buildCalendarKeyboard((int)$year, (int)$month);

        return new SendMessageContext($update->callbackQuery->message->chat->id, "📅 Оберіть дату подорожі:", $keyboard);
    }
}
