<?php

namespace App\Service\FlowStepService;

use App\Service\FlowStepServiceInterface;

interface StateAwareFlowStepServiceInterface extends FlowStepServiceInterface
{
    public function supportsStates(): array;
}
