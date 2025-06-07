<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\KeyboardService\BuildCalendarKeyboardTrait;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\GetTripStyleKeyboardTrait;
use App\Service\UserStateStorage;

class StopCustomDurationService implements StateAwareFlowStepServiceInterface
{
    use GetTripStyleKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->message
            && States::WaitingForStopCustomDuration === $this->userStateStorage->getState($update->message->chat->id);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStopCustomDuration];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (!is_numeric($update->message->text) || $update->message->text < 0 || $update->message->text >= 30) {
            return new SendMessageContext($chatId, "Будь ласка, введіть число від 1 до 30.", null, States::WaitingForStopCustomDuration);
        }

        $context->currentStopDraft->duration = (int) $update->message->text;
        $this->userStateStorage->saveContext($chatId, $context);

        $text = "Чудово! Подорож на {$context->duration} днів. Тепер оберіть дати поїздки. \n\n📅 Оберіть стиль подорожі для цієї зупинки:";

        return new SendMessageContext($chatId, $text, $this->getTripStyleKeyboard(), States::WaitingForStopTripStyle);
    }
}
