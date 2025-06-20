<?php

namespace App\Service\TripPlanner;

use App\DTO\TripPlan\StopPlan;

interface DailyScheduleFormatterInterface
{
    public function format(StopPlan $stop, array $activities, array $foodPlaces): array;
}
