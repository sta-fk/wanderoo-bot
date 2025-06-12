<?php

namespace App\Service\TripPlanner;

use App\DTO\DayPlan;
use App\DTO\StopContext;
use App\Service\TripPlanner\DailyScheduleFormatterInterface;

class SimpleDailyScheduleFormatter implements DailyScheduleFormatterInterface
{
    public function format(StopContext $stop, array $activities, array $foodPlaces): array
    {
        $days = [];
        $duration = (int) $stop->startDate->diff($stop->endDate)->format('%a') + 1;

        for ($i = 0; $i < $duration; $i++) {
            $date = (clone $stop->startDate)->modify("+{$i} day");

            $day = new DayPlan();
            $day->date = $date;

            $style = $stop->style ?? 'balanced';
            $activityCount = match ($style) {
                'active', 'mixed', 'cultural' => 4,
                'roadtrip' => 3,
                'relax', 'budget' => 5,
                default => 2,
            };
            $foodCount = 2;

            $day->activities = array_slice($activities, $i * $activityCount, $activityCount);
            $day->foodPlaces = array_slice($foodPlaces, $i * $foodCount, $foodCount);

            $days[] = $day;
        }

        return $days;
    }
}
