<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\DTO\StopContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardService\CountryKeyboardProvider;
use App\Service\UserStateStorage;

readonly class AddStopCallbackService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private CountryKeyboardProvider $countryKeyboardProvider,
    ) {}

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && $update->callbackQuery->data === CallbackQueryData::AddStop->value;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;

        $context = $this->userStateStorage->getContext($chatId);
        $context->currentStopDraft = new StopContext();
        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "Оберіть країну для нової зупинки:",
            $this->countryKeyboardProvider->provideDefaultKeyboard(),
            States::WaitingForStopCountry
        );
    }
}
