<?php

namespace App\Service\TripPlanner;

use App\DTO\TripPlan;

interface TripPlanFormatterInterface
{
    public function format(TripPlan $plan): string;
}
