<?php

namespace App\Service\FlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardResolver\KeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class NewTripService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private KeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::NewTrip->value === $update->callbackQuery->data;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;

        $this->userStateStorage->clearContext($chatId);
        $this->userStateStorage->saveContext($chatId, new PlanContext());

        $keyboardProvider = $this->keyboardProviderResolver->resolve($update);

        return new SendMessageContext(
            $chatId,
            $keyboardProvider->getTextMessage(),
            $keyboardProvider->buildKeyboard(),
            States::WaitingForCountry
        );
    }
}
