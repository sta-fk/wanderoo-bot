<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\UserStateStorage;

class CustomDurationService implements StateAwareFlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->message
            && States::WaitingForCustomDuration === $this->userStateStorage->getState($update->message->chat->id);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCustomDuration];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (!is_numeric($update->message->text) || $update->message->text < 0 || $update->message->text >= 30) {
            return new SendMessageContext($chatId, "Будь ласка, введіть число від 1 до 30.", null, States::WaitingForCustomDuration);
        }

        $context->duration = (int) $update->message->text;
        $this->userStateStorage->saveContext($chatId, $context);

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $keyboard = $this->buildCalendarKeyboard($now->format('Y'), $now->format('m'));
        $text = "Чудово! Подорож на {$context->duration} днів. Тепер оберіть дати поїздки. \n\n 📅 Оберіть дату подорожі:";

        return new SendMessageContext($chatId, $text, $keyboard, States::WaitingForStartDate);
    }
}
