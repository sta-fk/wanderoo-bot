<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

readonly class CityService implements StatefulFlowStepServiceInterface
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
        return States::WaitingForDuration;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $cityName = substr($update->callbackQuery->data, strlen(CallbackQueryData::City->value));
        $context->city = $cityName;
        $this->userStateStorage->saveContext($chatId, $context);

        $keyboard = [
            'inline_keyboard' => [
                [['text' => '1 день', 'callback_data' => CallbackQueryData::Duration->value.'1']],
                [['text' => '3 дні', 'callback_data' => CallbackQueryData::Duration->value.'3']],
                [['text' => '5 днів', 'callback_data' => CallbackQueryData::Duration->value.'5']],
                [['text' => '7 днів', 'callback_data' => CallbackQueryData::Duration->value.'7']],
                [['text' => '🔢 Інший варіант', 'callback_data' => CallbackQueryData::Duration->value.'custom']],
            ]
        ];

        return new SendMessageContext(
            $chatId,
            "Ви обрали місто: {$cityName}. На скільки днів плануєте подорож?",
            $keyboard
        );
    }
}
