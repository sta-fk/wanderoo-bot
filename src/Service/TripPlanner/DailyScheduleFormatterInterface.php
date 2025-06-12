<?php

namespace App\Service\TripPlanner;

use App\DTO\StopPlan;

interface DailyScheduleFormatterInterface
{
    public function format(StopPlan $stop, array $activities, array $foodPlaces): array;
}
