<?php

namespace App\Service\FlowStepService;

interface StateAwareViewDataBuilderInterface extends ViewDataBuilderInterface
{
    public function supportsStates(): array;
}
