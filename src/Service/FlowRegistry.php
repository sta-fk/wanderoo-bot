<?php

namespace App\Service;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Request\TelegramUpdate;
use App\Enum\MessageView;
use App\Service\Draft\FlowStepService\StateAwareFlowViewDataServiceInterface;
use App\Service\FlowStepService\FlowViewDataServiceInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class FlowRegistry
{
    public function __construct(
        #[AutowireIterator('flow_view_data_service')]
        private iterable $flowViewDataServices,
        private UserStateStorage $userStateStorage
    ) {}

    public function findMatchingService(TelegramUpdate $update): ?FlowViewDataServiceInterface
    {
        $chatId = $update->callbackQuery->message->chat->id ?? $update->message->chat->id ?? null;
        $currentState = $chatId ? $this->userStateStorage->getState($chatId) : null;

        /** @var FlowViewDataServiceInterface $flowViewDataService */
        foreach ($this->flowViewDataServices as $flowViewDataService) {
            if (
                $flowViewDataService instanceof StateAwareFlowViewDataServiceInterface
                && $flowViewDataService->supportsUpdate($update)
            ) {
                if ($currentState !== null && in_array($currentState, $flowViewDataService->supportsStates(), true)) {
                    return $flowViewDataService;
                }
            } elseif ($flowViewDataService->supportsUpdate($update)) {
                return $flowViewDataService;
            }
        }

        return null;
    }

    public function resolveMessageViewIdentifier(MessageView $view): MessageViewIdentifier
    {
        return new MessageViewIdentifier($view->value);
    }
}
