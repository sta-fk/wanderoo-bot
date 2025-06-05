<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

readonly class CityService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::City->value)
            && !strpos($update->callbackQuery->data, 'page')
        ;
    }

    public function getNextState(): States
    {
        return States::ReadyForDates;
    }

    public function buildMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $cityName = substr($update->callbackQuery->data, 5);
        $context->city = $cityName;
        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext($update->callbackQuery->message->chat->id, "Ви обрали місто: {$cityName}. Тепер оберіть дати.");
    }
}
