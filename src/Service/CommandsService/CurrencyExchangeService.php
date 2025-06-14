<?php

namespace App\Service\CommandsService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardProviderResolver;
use App\Service\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class CurrencyExchangeService implements FlowStepServiceInterface
{
    public function __construct(
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return TelegramCommands::Exchanger->value === $update->message?->text;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $keyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForExchangeChoice);

        return new SendMessageContext(
            $update->message->chat->id,
            $keyboardProvider->getTextMessage(),
            $keyboardProvider->buildKeyboard(),
            States::WaitingForExchangeChoice
        );
    }
}
