<?php

namespace App\Service;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Request\TelegramUpdate;
use App\Enum\View;
use App\Service\Draft\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\FlowStepService\FlowStepServiceInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class FlowRegistry
{
    public function __construct(
        #[AutowireIterator('flow_step_service')]
        private iterable $flowStepsServices,
        private UserStateStorage $userStateStorage
    ) {}

    public function findMatchingService(TelegramUpdate $update): ?FlowStepServiceInterface
    {
        $chatId = $update->callbackQuery->message->chat->id ?? $update->message->chat->id ?? null;
        $currentState = $chatId ? $this->userStateStorage->getState($chatId) : null;

        /** @var FlowStepServiceInterface $flowStepService */
        foreach ($this->flowStepsServices as $flowStepService) {
            if (
                $flowStepService instanceof StateAwareFlowStepServiceInterface
                && $flowStepService->supports($update)
            ) {
                if ($currentState !== null && in_array($currentState, $flowStepService->supportsStates(), true)) {
                    return $flowStepService;
                }
            } elseif ($flowStepService->supports($update)) {
                return $flowStepService;
            }
        }

        return null;
    }

    public function resolveMessageViewIdentifier(View $view): MessageViewIdentifier
    {
        return new MessageViewIdentifier($view->value);
    }
}
