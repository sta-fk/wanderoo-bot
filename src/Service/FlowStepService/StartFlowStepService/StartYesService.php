<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\DTO\StopContext;
use App\Enum\States;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class StartYesService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::StartYes->value === $update->callbackQuery->data
        ;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStart];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = new PlanContext();
        $context->currentStopDraft = new StopContext();

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "Супер, поїхали ✨! Введіть назву країни (або частину назви):",
            null,
            States::WaitingForCountry
        );
    }
}
