<?php

namespace App\Service\TripPlanner;

use App\DTO\StopPlan;
use App\DTO\TripPlan;
use IntlDateFormatter;

readonly class SimpleTripPlanFormatter implements TripPlanFormatterInterface
{
    public function __construct(
        private string $locale = 'uk_UA'
    ) {}

    public function format(TripPlan $plan): string
    {
        $formatter = new IntlDateFormatter(
            $this->locale,
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            $plan->startDate->getTimezone()->getName(),
            null,
            'd MMMM'
        );

        $message = "<b>🧭 Ваш маршрут:</b>\n";
        $message .= sprintf("📅 %s — %s\n",
            $formatter->format($plan->startDate),
            $formatter->format($plan->endDate)
        );
        $message .= sprintf("💰 Загальний бюджет: %s%s\n", $plan->totalBudget, $plan->currency);
        $message .= sprintf("🕒 Тривалість: %d днів\n\n", $plan->totalDuration);

        foreach ($plan->stops as $index => $stop) {
            $message .= $this->formatStop($index + 1, $stop, $formatter) . "\n";
        }

        return trim($message);
    }

    private function formatStop(int $index, StopPlan $stop, IntlDateFormatter $formatter): string
    {
        $sameDay = $stop->startDate->format('Y-m-d') === $stop->endDate->format('Y-m-d');
        $dateRange = $sameDay
            ? $formatter->format($stop->startDate)
            : sprintf('%s — %s', $formatter->format($stop->startDate), $formatter->format($stop->endDate));

        $message = sprintf("<b>%d. %s, %s</b> (%s)\n", $index, $stop->cityName, $stop->countryName, $dateRange);

        if ($stop->localTransportInfo) {
            $message .= "🚍 <i>Транспорт:</i> " . htmlspecialchars($stop->localTransportInfo) . "\n";
        }

        foreach ($stop->days as $dayIndex => $day) {
            $message .= sprintf("\n📅 <b>День %d</b>", $dayIndex + 1);

            if ($day->date) {
                $message .= sprintf(" — <i>%s</i>\n", $formatter->format($day->date));
            } else {
                $message .= "\n";
            }

            if (!empty($day->activities)) {
                $message .= "🔹 <u>Активності:</u>\n";
                foreach ($day->activities as $activity) {
                    $message .= "• " . htmlspecialchars($activity) . "\n";
                }
            } else {
                $message .= "🔹 Активності: немає даних\n";
            }

            if (!empty($day->foodPlaces)) {
                $message .= "🍴 <u>Місця для їжі:</u>\n";
                foreach ($day->foodPlaces as $place) {
                    $message .= "• " . htmlspecialchars($place) . "\n";
                }
            } else {
                $message .= "🍴 Місця для їжі: немає даних\n";
            }
        }

        return trim($message);
    }
}
