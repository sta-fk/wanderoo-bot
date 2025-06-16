<?php

namespace App\Service\TripPlanner;

use App\DTO\TripPlan\DayPlan;
use App\DTO\TripPlan\StopPlan;

class SimpleDailyScheduleFormatter implements DailyScheduleFormatterInterface
{
    public function format(StopPlan $stop, array $activities, array $foodPlaces): array
    {
        $days = [];
        $duration = $stop->startDate->diff($stop->endDate)->days + 1;

        $style = $stop->style ?? 'balanced';
        $activityCount = match ($style) {
            'active', 'mixed', 'cultural' => 15,
            'relax', 'budget' => 10,
            default => 5,
        };

        $foodCount = $duration * 2;
        $activities = array_slice($activities, 0, $activityCount);
        $foodPlaces = array_slice($foodPlaces, 0, $foodCount);

        $daysWithFreeTime = (int) floor($duration / 4);
        $usedActivities = 0;
        $usedFood = 0;

        for ($i = 0; $i < $duration; $i++) {
            $day = new DayPlan();
            $date = (clone $stop->startDate)->modify("+{$i} day");
            $day->date = $date;

            $hasFreeTime = $daysWithFreeTime > 0 && ($usedActivities >= count($activities));

            if (!$hasFreeTime) {
                $day->activities = array_slice($activities, $usedActivities, 3);
                $usedActivities += 3;
            } else {
                $day->activities = [];
                $daysWithFreeTime--;
            }

            $day->foodPlaces = array_slice($foodPlaces, $usedFood, 2);
            $usedFood += 2;

            $days[] = $day;
        }

        return $days;
    }
}
