<?php

namespace App\Service\CommandsService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\DTO\StopContext;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardService\CountryKeyboardProvider;
use App\Service\UserStateStorage;

readonly class AddStopService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private CountryKeyboardProvider $countryKeyboardProvider,
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
        $context->currentStopDraft = new StopContext();
        $this->userStateStorage->saveContext($chatId, $context);

        $keyboard = $this->countryKeyboardProvider->provideDefaultKeyboard();

        return new SendMessageContext(
            $chatId,
            "Продовжимо подорож! 🌍\n\nОберіть країну для зупинки:",
            $keyboard,
            States::WaitingForStopCountry
        );
    }
}
