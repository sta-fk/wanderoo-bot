<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

class PickDateService implements FlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, CallbackQueryData::PickDate->value);
    }

    public function getNextState(): States
    {
        return States::WaitingForStyle;
    }

    public function buildMessage(TelegramUpdate $update): SendMessageContext
    {
        $dateStr = substr($update->callbackQuery->data, 10); // YYYY-MM-DD
        $chatId = $update->callbackQuery->message->chat->id;

        $context = $this->userStateStorage->getContext($chatId);
        if (null === $context->duration) {
            throw new \RuntimeException("Invalid payload");
        }

        $context->startDate = $dateStr;

        $endDate = (new \DateTimeImmutable($dateStr))->modify("+$context->duration days");
        $context->endDate = $endDate->format('Y-m-d');

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext($chatId, "✅ Подорож з <b>$dateStr</b> по <b>{$endDate->format('Y-m-d')}</b>");
    }
}
