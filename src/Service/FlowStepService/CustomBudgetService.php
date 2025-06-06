<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

class CustomBudgetService implements StatefulFlowStepServiceInterface
{
    private bool $validationPassed = false;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text
            && $this->userStateStorage->getState($update->message->chat->id) === States::WaitingForCustomBudget;
    }
    
    public function getNextState(): States
    {
        if (!$this->validationPassed) {
            return States::WaitingForCustomBudget;
        }

        $this->validationPassed = false;

        return States::ReadyToBuildPlan;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $userInput = preg_replace('/[^\d]/', '', $update->message->text);

        $context->budget = 'Не вказано';
        if ($userInput) {
            $this->validationPassed = true;

            $context->budget = "{$userInput}€ +/-";
        }

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "✅ Дякую! Орієнтовний бюджет: {$context->budget}.\n\nГотуємо для вас персоналізований план мандрівки... ✈️"
        );
    }
}
