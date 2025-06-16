<?php

namespace App\Service\Draft\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\Service\FlowStepServiceInterface;

interface FinalStateAwareFlowStepServiceInterface extends FlowStepServiceInterface
{
    public function getSplitFormattedPlan(TelegramUpdate $update): array;
}
