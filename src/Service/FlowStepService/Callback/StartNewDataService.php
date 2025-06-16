<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\StartNewViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\Context\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowViewDataServiceInterface;
use App\Service\UserStateStorage;

readonly class StartNewDataService implements StateAwareFlowViewDataServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->callbackQuery
            && CallbackData::StartNew->value === $update->callbackQuery->data;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStartNew];
    }

    public function buildNextStepViewData(TelegramUpdate $update): ViewDataInterface
    {
        $chatId = $update->callbackQuery->message->chat->id;

        $this->userStateStorage->clearContext($chatId);
        $this->userStateStorage->saveContext($chatId, new PlanContext());

        return new StartNewViewData($chatId, $update->callbackQuery->id);
    }
}
