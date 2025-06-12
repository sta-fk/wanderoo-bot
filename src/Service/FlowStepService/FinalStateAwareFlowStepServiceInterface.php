<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\Service\FlowStepServiceInterface;

interface FinalStateAwareFlowStepServiceInterface extends FlowStepServiceInterface
{
    public function getSplitFormattedPlan(TelegramUpdate $update): array;
}
