<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardService\CityKeyboardProvider;
use App\Service\KeyboardService\StopCityKeyboardProvider;
use App\Service\UserStateStorage;

readonly class StopCityWithPaginationService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private StopCityKeyboardProvider $stopCityKeyboardProvider,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::StopCityPage->value)
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);
        if (null === $context->currentStopDraft->country) {
            throw new \RuntimeException("Invalid payload");
        }

        $offset = (int) substr($update->callbackQuery->data, strlen(CallbackQueryData::StopCityPage->value));
        $keyboard = $this->stopCityKeyboardProvider->providePaginationKeyboard($context->currentStopDraft->country, $offset);

        return new SendMessageContext($update->callbackQuery->message->chat->id, "Ще 5 міст:", $keyboard);
    }
}
