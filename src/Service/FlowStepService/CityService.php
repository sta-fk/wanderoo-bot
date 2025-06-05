<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\UserStateStorage;

class CityService implements FlowStepServiceInterface
{
    private const CALLBACK_QUERY_DATA_STARTS_WITH = 'city_';

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, self::CALLBACK_QUERY_DATA_STARTS_WITH);
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

        return new SendMessageContext($update->message->chat->id, "Ви обрали місто: {$cityName}. Тепер оберіть дати.");
    }
}
