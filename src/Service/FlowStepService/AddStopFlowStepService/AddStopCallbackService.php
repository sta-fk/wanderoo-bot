<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\KeyboardProviderResolver;
use App\Service\FlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class AddStopCallbackService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private KeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::AddStop->value === $update->callbackQuery->data
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;

        $keyboardProvider = $this->keyboardProviderResolver->resolve($update);
        $text = $keyboardProvider->getTextMessage($chatId);
        $keyboard = $keyboardProvider->buildKeyboard($chatId);

        $context = $this->userStateStorage->getContext($chatId);
        $context->resetCurrentStopDraft();
        $context->enableAddingStopFlow();

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            $text,
            $keyboard,
            States::WaitingForStopCountry
        );
    }
}
