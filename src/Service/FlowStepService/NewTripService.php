<?php

namespace App\Service\FlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class NewTripService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::NewTrip->value === $update->callbackQuery->data;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = new PlanContext();

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "Розпочнімо нову подорож! 🌍\n\nСпершу введіть назву країни (або частину назви):",
            null,
            States::WaitingForCountry
        );
    }
}
