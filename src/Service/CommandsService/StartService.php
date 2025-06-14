<?php

namespace App\Service\CommandsService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardResolver\KeyboardProviderResolver;

readonly class StartService implements FlowStepServiceInterface
{
    public function __construct(private KeyboardProviderResolver $keyboardProviderResolver)
    {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text === TelegramCommands::Start->value;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $keyboardProvider = $this->keyboardProviderResolver->resolve($update);

        return new SendMessageContext(
            $update->message->chat->id,
            $keyboardProvider->getTextMessage(),
            $keyboardProvider->buildKeyboard(),
            States::WaitingForStart
        );
    }
}
