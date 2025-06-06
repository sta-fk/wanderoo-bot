<?php

namespace App\Service;

use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\FlowStepServiceInterface;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class FlowRegistry
{
    public function __construct(
        #[AutowireIterator('flow_step_service')]
        private readonly iterable $flowStepsServices,
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function findMatchingService(TelegramUpdate $update): ?FlowStepServiceInterface
    {
        $chatId = $update->callbackQuery->message->chat->id ?? $update->message->chat->id ?? null;
        $currentState = $chatId ? $this->userStateStorage->getState($chatId) : null;

        /** @var FlowStepServiceInterface $flowStepService */
        foreach ($this->flowStepsServices as $flowStepService) {

            if ($flowStepService instanceof StateAwareFlowStepServiceInterface && $currentState !== null && in_array($currentState, $flowStepService->supportsStates(), true)) {
                return $flowStepService;
            }

            if ($flowStepService->supports($update)) {
                return $flowStepService;
            }
        }

        return null;
    }
}
