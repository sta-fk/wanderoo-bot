<?php

namespace App\Service\Draft\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\FlowStepService\StateAwareFlowViewDataServiceInterface;
use App\Service\Draft\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class DurationService implements StateAwareFlowViewDataServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Duration->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForDuration];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $durationValue = substr($update->callbackQuery->data, strlen(CallbackQueryData::Duration->value));
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (CallbackQueryData::Custom->value === $durationValue) {
            $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCustomDuration);

            return new SendMessageContext(
                $chatId,
                $nextStateKeyboardProvider->getTextMessage(),
                $nextStateKeyboardProvider->buildKeyboard(),
                States::WaitingForCustomDuration
            );
        }

        $context->currentStopDraft->duration = (int) $durationValue;

        $this->userStateStorage->saveContext($chatId, $context);

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForStartDate);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage($chatId),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForStartDate
        );
    }
}
