<?php

namespace App\Service\Draft\FlowStepService;

use App\Service\FlowStepService\FlowStepServiceInterface;

interface StateAwareFlowStepServiceInterface extends FlowStepServiceInterface
{
    public function supportsStates(): array;
}
