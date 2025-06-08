<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardService\CountryKeyboardProvider;
use App\Service\KeyboardService\StopCountryKeyboardProvider;

readonly class StopCountryWithPaginationService implements FlowStepServiceInterface
{
    public function __construct(
        private StopCountryKeyboardProvider $stopCountryKeyboardProvider,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::StopCountryPage->value)
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $offset = (int) substr($update->callbackQuery->data, strlen(CallbackQueryData::StopCountryPage->value));
        $keyboard = $this->stopCountryKeyboardProvider->providePaginationKeyboard($offset);

        return new SendMessageContext($update->callbackQuery->message->chat->id, "Ще 5 країн:", $keyboard);
    }
}
