<?php

namespace App\Service\TripPlanner;

use App\DTO\StopContext;

interface DailyScheduleFormatterInterface
{
    public function format(StopContext $stop, array $activities, array $foodPlaces): array;
}
