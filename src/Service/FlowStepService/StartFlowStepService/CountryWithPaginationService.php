<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardService\CountryKeyboardProvider;

readonly class CountryWithPaginationService implements FlowStepServiceInterface
{
    public function __construct(
        private CountryKeyboardProvider $countryKeyboardProvider,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::CountryPage->value)
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $offset = (int) substr($update->callbackQuery->data, strlen(CallbackQueryData::CountryPage->value));
        $keyboard = $this->countryKeyboardProvider->providePaginationKeyboard($offset);

        return new SendMessageContext($update->callbackQuery->message->chat->id, "Ще 5 країн:", $keyboard);
    }
}
