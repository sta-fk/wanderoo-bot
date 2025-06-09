<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StartFlowStepService\BudgetService;
use App\Service\FlowStepService\StartFlowStepService\InterestsService;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\BuildInterestsKeyboardTrait;
use App\Service\KeyboardService\GetTripStyleKeyboardTrait;
use App\Service\UserStateStorage;

class ReuseOrNewInterestsService implements StateAwareFlowStepServiceInterface
{
    use BuildInterestsKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Interest->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForReuseOrNewInterests];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $action = substr($update->callbackQuery->data, strlen(CallbackQueryData::TripStyle->value));

        if ($action === CallbackQueryData::Reuse->value) {
            $lastOneStop = ($context->stops[count($context->stops) - 1]);
            $currentStopDraft = $context->currentStopDraft;

            $currentStopDraft->interests = $lastOneStop->interests;
            $this->userStateStorage->saveContext($chatId, $context);

            $selectedLabels = array_map(
                static fn ($key) => strtolower(InterestsService::INTERESTS[$key]) ?? $key,
                $context->currentStopDraft->interests ?? []
            );

            return new SendMessageContext(
                $chatId,
                "Чудово! Інтереси для {$currentStopDraft->cityName}: " . implode(', ', $selectedLabels) . ".\n\n✍️ Введіть бажаний бюджет у євро (наприклад: <b>100</b>):",
                null,
                States::WaitingForCustomBudget
            );
        }

        $keyboard = $this->buildInterestsKeyboard(
            $context->currentStopDraft->interests,
            InterestsService::INTERESTS
        );

        return new SendMessageContext(
            $chatId,
            "✨ Що вас цікавить в {$context->currentStopDraft->cityName}? Оберіть кілька варіантів:",
            $keyboard,
            States::WaitingForInterests
        );

    }
}
