<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\CityKeyboardProvider;
use App\Service\UserStateStorage;

readonly class CountryService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage     $userStateStorage,
        private CityKeyboardProvider $cityKeyboardProvider,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Country->value)
            && !strpos($update->callbackQuery->data, 'page')
        ;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCountry];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $countryCode = substr($update->callbackQuery->data, strlen(CallbackQueryData::Country->value));
        $context->country = $countryCode;
        $this->userStateStorage->saveContext($chatId, $context);

        $keyboard = $this->cityKeyboardProvider->provideDefaultKeyboard($countryCode);

        return new SendMessageContext($chatId, "🚀Оберіть місто:", $keyboard, States::WaitingForCity);
    }
}
