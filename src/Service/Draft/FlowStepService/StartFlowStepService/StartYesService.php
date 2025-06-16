<?php

namespace App\Service\Draft\FlowStepService\StartFlowStepService;

use App\DTO\Context\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Enum\CallbackQueryData;
use App\Service\Draft\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\Draft\FlowStepService\StateAwareFlowViewDataServiceInterface;
use App\Service\UserStateStorage;

readonly class StartYesService implements StateAwareFlowViewDataServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::StartYes->value === $update->callbackQuery->data
        ;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStartNew];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = new PlanContext();

        $this->userStateStorage->saveContext($chatId, $context);

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCountryName);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage(),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForCountryName
        );
    }
}
