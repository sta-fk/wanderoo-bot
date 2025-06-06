<?php

namespace App\Service\FlowStepService;

use App\Enum\States;

interface StatefulFlowStepServiceInterface extends FlowStepServiceInterface
{
    public function getNextState(): States;
}
