<?php

namespace App\Service\TripPlanner;

use App\DTO\StopPlan;
use App\DTO\TripPlan;

class SimpleTripPlanFormatter implements TripPlanFormatterInterface
{
    public function format(TripPlan $plan): string
    {
        $message = "🧭 *Ваш маршрут:*\n";
        $message .= sprintf("📅 %s — %s\n", $plan->startDate->format('d-M'), $plan->endDate->format('d-M'));
        $message .= sprintf("💰 Загальний бюджет: %s%s\n", $plan->totalBudget, $plan->currency);
        $message .= sprintf("🕒 Тривалість: %d днів\n\n", $plan->totalDuration);

        foreach ($plan->stops as $index => $stop) {
            $message .= $this->formatStop($index + 1, $stop) . "\n\n";
        }

        return trim($message);
    }

    private function formatStop(int $index, StopPlan $stop): string
    {
        $message = sprintf("*%d. %s, %s (%s — %s)*\n", $index, $stop->cityName, $stop->countryName, $stop->startDate->format('d-M'), $stop->endDate->format('d-M'));

        if ($stop->localTransportInfo) {
            $message .= "🚍 Транспорт: " . $stop->localTransportInfo . "\n";
        }

        if (!empty($stop->days)) {
            foreach ($stop->days as $dayIndex => $day) {
                $message .= sprintf("📅 День %d:\n", $dayIndex + 1);

                if (!empty($day->activities)) {
                    $message .= "🔹 Активності:\n";
                    foreach ($day->activities as $activity) {
                        $message .= "- " . $activity . "\n";
                    }
                }

                if (!empty($day->foodPlaces)) {
                    $message .= "🍴 Місця для їжі:\n";
                    foreach ($day->foodPlaces as $place) {
                        $message .= "- " . $place . "\n";
                    }
                }
            }
        }

        return trim($message);
    }
}
