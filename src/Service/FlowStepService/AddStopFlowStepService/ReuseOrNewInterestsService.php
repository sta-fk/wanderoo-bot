<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class ReuseOrNewInterestsService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Interest->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForReuseOrNewInterests];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $action = substr($update->callbackQuery->data, strlen(CallbackQueryData::Interest->value));

        if ($action === CallbackQueryData::Reuse->value) {
            $lastOneStop = ($context->stops[count($context->stops) - 1]);
            $currentStopDraft = $context->currentStopDraft;

            $currentStopDraft->interests = $lastOneStop->interests;
            $this->userStateStorage->saveContext($chatId, $context);

            $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCustomBudget);

            return new SendMessageContext(
                $chatId,
                $nextStateKeyboardProvider->getTextMessage($chatId),
                $nextStateKeyboardProvider->buildKeyboard(),
                States::WaitingForCustomBudget
            );
        }

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForInterests);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage($chatId),
            $nextStateKeyboardProvider->buildKeyboard($context->currentStopDraft->interests),
            States::WaitingForInterests
        );

    }
}
