<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

class BudgetService implements StatefulFlowStepServiceInterface
{
    public const BUDGET_OPTIONS = [
        'none' => 'Без бюджету',
        '0_300' => 'До 300€',
        '300_700' => '300€ — 700€',
        '700_1500' => '700€ — 1500€',
        '1500_plus' => 'Понад 1500€',
        'custom' => 'Інша сума',
    ];

    private bool $neededCustomBudget = false;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->callbackQuery && str_starts_with($update->callbackQuery->data, CallbackQueryData::Budget->value);
    }

    public function getNextState(): States
    {
        if (!$this->neededCustomBudget) {
            return States::ReadyToBuildPlan;
        }

        $this->neededCustomBudget = false;

        return States::WaitingForCustomBudget;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $budgetKey = substr($update->callbackQuery->data, strlen(CallbackQueryData::Budget->value));

        if ('custom' === $budgetKey) {
            $this->neededCustomBudget = true;

            return new SendMessageContext($chatId, "✍️ Введіть бажаний бюджет у євро (наприклад: <b>500</b>):");
        }

        $context->budget = self::BUDGET_OPTIONS[$budgetKey] ?? 'Не вказано';
        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "✅ Дякую! Орієнтовний бюджет: {$context->budget}.\n\nГотуємо для вас персоналізований план мандрівки... ✈️"
        );
    }

}
