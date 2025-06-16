<?php

namespace App\Service\FlowStepService;

interface StateAwareFlowViewDataServiceInterface extends FlowViewDataServiceInterface
{
    public function supportsStates(): array;
}
