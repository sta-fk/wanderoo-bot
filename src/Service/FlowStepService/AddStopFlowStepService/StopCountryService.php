<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\KeyboardService\BuildKeyboardTrait;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\CityKeyboardProvider;
use App\Service\UserStateStorage;

readonly class StopCountryService implements StateAwareFlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public function __construct(
        private UserStateStorage $userStateStorage,
        private CityKeyboardProvider $cityKeyboardProvider,
    ) {}

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::StopCountry->value)
            && !strpos($update->callbackQuery->data, 'page');
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStopCountry];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $countryCode = substr($update->callbackQuery->data, strlen(CallbackQueryData::StopCountry->value));

        $context = $this->userStateStorage->getContext($chatId);
        $context->currentStopDraft->country = $countryCode;
        $this->userStateStorage->saveContext($chatId, $context);

        $keyboard = $this->cityKeyboardProvider->provideDefaultKeyboard($countryCode);

        return new SendMessageContext($chatId, "🚀Чудово! Тепер оберіть місто:", $keyboard, States::WaitingForStopCity);

    }
}
