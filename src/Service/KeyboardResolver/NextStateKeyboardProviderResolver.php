<?php

namespace App\Service\KeyboardResolver;

use App\Enum\States;
use App\Service\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class NextStateKeyboardProviderResolver
{
    public function __construct(
        #[AutowireIterator('flow_step_keyboard_provider')]
        private iterable $flowStepKeyboardProviders,
    ) {
    }

    public function resolve(States $nextState): NextStateKeyboardProviderInterface
    {
        /** @var NextStateKeyboardProviderInterface $flowStepKeyboardProvider */
        foreach ($this->flowStepKeyboardProviders as $flowStepKeyboardProvider) {
            if ($flowStepKeyboardProvider->supports($nextState)) {
                return $flowStepKeyboardProvider;
            }
        }

        throw new \RuntimeException("Invalid state");
    }
}
