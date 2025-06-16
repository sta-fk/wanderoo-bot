<?php

namespace App\Service\Draft\FlowStepService\StartFlowStepService;

use App\DTO\Context\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\Draft\FlowStepService\StateAwareFlowViewDataServiceInterface;
use App\Service\Draft\KeyboardProvider\NextState\WaitingForCustomDurationKeyboardProvider;
use App\Service\Draft\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class CustomDurationService implements StateAwareFlowViewDataServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
        private WaitingForCustomDurationKeyboardProvider $customDurationKeyboardProvider,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->message;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCustomDuration];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (!is_numeric($update->message->text) || $update->message->text < 0 || $update->message->text >= 30) {
            return new SendMessageContext(
                $chatId,
                $this->customDurationKeyboardProvider->getValidationFailedMessage(),
                $this->customDurationKeyboardProvider->buildKeyboard(),
                States::WaitingForCustomDuration
            );
        }

        $context->currentStopDraft->duration = (int)$update->message->text;

        $this->userStateStorage->saveContext($chatId, $context);

        return $this->getSendMessageContext($chatId, $context);
    }

    private function getSendMessageContext(int $chatId, PlanContext $context): SendMessageContext
    {
        if ($context->isAddingStopFlow) {
            $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForReuseOrNewTripStyle);

            return new SendMessageContext(
                $chatId,
                $nextStateKeyboardProvider->getTextMessage($chatId),
                $nextStateKeyboardProvider->buildKeyboard(),
                States::WaitingForReuseOrNewTripStyle
            );
        }

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForStartDate);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage($chatId),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForStartDate
        );
    }
}
