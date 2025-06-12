<?php

namespace App\Service\TripPlanner;

use App\DTO\DayPlan;
use App\DTO\StopPlan;

class SimpleDailyScheduleFormatter implements DailyScheduleFormatterInterface
{
    public function format(StopPlan $stopPlan, array $activities, array $foodPlaces): array
    {
        $days = [];
        $startDate = $stopPlan->startDate;
        $endDate = $stopPlan->endDate;

        $duration = $startDate->diff($endDate)->days + 1;

        for ($i = 0; $i < $duration; $i++) {
            $date = $startDate->modify("+{$i} days");

            $day = new DayPlan();
            $day->date = $date;

            $style = $stopPlan->style ?? 'balanced';
            $activityCount = match ($style) {
                'active', 'mixed', 'cultural' => 15,
                'relax', 'budget' => 10,
                default => 5,
            };
            $foodCount = 2;

            $day->activities = array_slice($activities, $i * $activityCount, $activityCount);
            $day->foodPlaces = array_slice($foodPlaces, $i * $foodCount, $foodCount);

            $days[] = $day;
        }

        return $days;
    }
}
