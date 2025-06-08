<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\GetConfirmStopKeyboardTrait;
use App\Service\UserStateStorage;

readonly class StopCustomBudgetService implements StateAwareFlowStepServiceInterface
{
    use GetConfirmStopKeyboardTrait;

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text
            && $this->userStateStorage->getState($update->message->chat->id) === States::WaitingForStopCustomBudget;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStopCustomBudget];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $userInput = preg_replace('/[^\d]/', '', $update->message->text);

        if (!is_numeric($userInput)) {
            return new SendMessageContext(
                $chatId,
                "Не вдалося перетворити на цифру. Повторіть спробу.",
                null,
                States::WaitingForStopCustomBudget
            );
        }

        $context->currentStopDraft->budget = (int) $userInput;
        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "✅ Дякую! Орієнтовний бюджет: {$context->currentStopDraft->budget}€ +/-.\n\n Тепер підтвердьте зупинку або додайте ще одну.",
            $this->getConfirmKeyboard(),
            States::WaitingForConfirmStop
        );
    }
}
