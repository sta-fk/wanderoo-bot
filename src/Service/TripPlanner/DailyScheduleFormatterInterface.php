<?php

namespace App\Service\TripPlanner;

use App\DTO\StopPlan;

interface DailyScheduleFormatterInterface
{
    public function format(StopPlan $stopPlan, array $activities, array $foodPlaces): array;
}
