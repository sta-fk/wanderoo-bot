<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\CountryKeyboardProvider;
use App\Service\UserStateStorage;

readonly class StartYesService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private CountryKeyboardProvider $countryKeyboardProvider,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::StartYes->value === $update->callbackQuery->data
        ;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStart];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $this->userStateStorage->saveContext($chatId, new PlanContext());

        $keyboard = $this->countryKeyboardProvider->provideDefaultKeyboard();

        return new SendMessageContext($chatId, "Супер, поїхали ✨! Обери країну:", $keyboard, States::WaitingForCountry);
    }
}
