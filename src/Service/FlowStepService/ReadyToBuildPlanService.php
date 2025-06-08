<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepServiceInterface;

class ReadyToBuildPlanService implements FlowStepServiceInterface
{
    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && $update->callbackQuery->data === CallbackQueryData::ReadyToBuildPlan->value;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        return new SendMessageContext(
            $update->callbackQuery->message->chat->id,
            "Готуємо для вас персоналізований план мандрівки... ✈️"
        );
    }
}
