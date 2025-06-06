<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

class CustomDurationService implements FlowStepServiceInterface
{
    use BuildKeyboardTrait;

    private bool $validationPassed = false;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->message
            && States::WaitingForCustomDuration === $this->userStateStorage->getState($update->message->chat->id);
    }

    public function getNextState(): States
    {
        if (!$this->validationPassed) {
            return States::WaitingForCustomDuration;
        }

        $this->validationPassed = false;

        return States::WaitingForStartDate;
    }

    public function buildMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (null !== $update->message->text && (int)$update->message->text > 0 && (int)$update->message->text <= 30) {
            $this->validationPassed = true;
            $context->duration = (int) $update->message->text;
            $this->userStateStorage->saveContext($chatId, $context);

            $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
            $keyboard = $this->buildCalendarKeyboard($now->format('Y'), $now->format('m'));
            $text = <<<TEXT
Чудово! Подорож на {$context->duration} днів. Тепер оберіть дати поїздки.

"📅 Оберіть дату подорожі:
TEXT;

            return new SendMessageContext($chatId, $text, $keyboard);
        }

        return new SendMessageContext($chatId, "Будь ласка, введіть число від 1 до 30.");
    }
}
