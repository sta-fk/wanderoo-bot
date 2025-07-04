<?php

namespace App\Service;

use App\DTO\Request\TelegramUpdate;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class ViewDataRegistry
{
    public function __construct(
        #[AutowireIterator('view_data_builder')]
        private iterable $viewDataBuilders,
        private UserStateStorage $userStateStorage
    ) {
    }

    public function findMatchingBuilder(TelegramUpdate $update): ?ViewDataBuilderInterface
    {
        $chatId = $update->getChatId();
        $currentState = $chatId ? $this->userStateStorage->getState($chatId) : null;

        /** @var ViewDataBuilderInterface $viewDataBuilder */
        foreach ($this->viewDataBuilders as $viewDataBuilder) {
            if (
                $viewDataBuilder instanceof StateAwareViewDataBuilderInterface
                && $viewDataBuilder->supportsUpdate($update)
            ) {
                if ($currentState !== null && in_array($currentState, $viewDataBuilder->supportsStates(), true)) {
                    return $viewDataBuilder;
                }
            } elseif ($viewDataBuilder->supportsUpdate($update)) {
                return $viewDataBuilder;
            }
        }

        return null;
    }
}
