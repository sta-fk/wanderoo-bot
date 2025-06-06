<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

class DurationService implements StatefulFlowStepServiceInterface
{
    use BuildKeyboardTrait;

    private bool $neededCustomDuration = false;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, CallbackQueryData::Duration->value);
    }

    public function getNextState(): States
    {
        if (!$this->neededCustomDuration) {
            return States::WaitingForStartDate;
        }

        $this->neededCustomDuration = false;

        return States::WaitingForCustomDuration;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $durationValue = substr($update->callbackQuery->data, strlen(CallbackQueryData::Duration->value));
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if ('custom' === $durationValue) {
            $this->neededCustomDuration = true;

            return new SendMessageContext($chatId, "Введіть кількість днів (наприклад, 4):");
        }

        $context->duration = (int) $durationValue;
        $this->userStateStorage->saveContext($chatId, $context);

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $keyboard = $this->buildCalendarKeyboard($now->format('Y'), $now->format('m'));
        $text = "Чудово! Подорож на {$durationValue} днів. Тепер оберіть дати поїздки. \n\n📅 Оберіть дату подорожі:";

        return new SendMessageContext($chatId, $text, $keyboard);
    }
}
