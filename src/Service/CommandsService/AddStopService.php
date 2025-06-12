<?php

namespace App\Service\CommandsService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardProvider\Message\StartMessageKeyboardProvider;
use App\Service\KeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class AddStopService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private KeyboardProviderResolver $keyboardProviderResolver,
        private StartMessageKeyboardProvider $startMessageKeyboardProvider,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text === TelegramCommands::AddStop->value;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;

        $context = $this->userStateStorage->getContext($chatId);

        if (null === $context->currentStopDraft->countryName) {
            return new SendMessageContext(
                $chatId,
                "В тебе немає поточної поїздки \n\n Почнемо?",
                $this->startMessageKeyboardProvider->buildKeyboard(),
                States::WaitingForStart
            );
        }

        $keyboardProvider = $this->keyboardProviderResolver->resolve($update);
        $text = $keyboardProvider->getTextMessage($chatId);
        $keyboard = $keyboardProvider->buildKeyboard($chatId);

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
