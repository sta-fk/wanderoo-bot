<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\GetDurationKeyboardTrait;
use App\Service\UserStateStorage;

readonly class StopCityService implements StateAwareFlowStepServiceInterface
{
    use GetDurationKeyboardTrait;

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::StopCity->value)
            && !strpos($update->callbackQuery->data, 'page')
        ;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStopCity];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $cityName = substr($update->callbackQuery->data, strlen(CallbackQueryData::StopCity->value));
        $context->currentStopDraft->city = $cityName;
        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "Скільки днів ви плануєте провести у {$cityName}?",
            $this->getDurationKeyboard(),
            States::WaitingForStopDuration
        );
    }
}
