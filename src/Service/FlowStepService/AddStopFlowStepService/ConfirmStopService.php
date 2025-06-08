<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class ConfirmStopService implements StateAwareFlowStepServiceInterface
{
    public function __construct(private UserStateStorage $userStateStorage) {}

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && $update->callbackQuery->data === CallbackQueryData::ConfirmStop->value;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForConfirmStop];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (null !== $context->currentStopDraft) {
            $context->tripStops[] = $context->currentStopDraft;
            $context->currentStopDraft = null;
            $this->userStateStorage->saveContext($chatId, $context);
        }

        return new SendMessageContext(
            $chatId,
            "Зупинку додано ✅! Готуємо для вас персоналізований план мандрівки... ✈️",
            null,
            States::ReadyToBuildPlan
        );
    }
}
