<?php

namespace App\Service\TripPlanner;

use App\DTO\StopContext;
use App\DTO\StopPlan;

interface StopPlanGeneratorInterface
{
    public function generate(StopContext $stop): StopPlan;
}
