<?php

namespace App\Service\TripPlanner;

use App\DTO\Context\StopContext;
use App\DTO\TripPlan\StopPlan;

interface StopPlanGeneratorInterface
{
    public function generate(StopContext $stop): StopPlan;
}
