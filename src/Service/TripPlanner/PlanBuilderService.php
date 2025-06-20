<?php

namespace App\Service\TripPlanner;

use App\DTO\Context\PlanContext;
use App\DTO\TripPlan\TripPlan;
use App\Service\Integrations\PoiProviderInterface;

readonly class PlanBuilderService
{
    public function __construct(
        private StopPlanGeneratorInterface $stopPlanGenerator,
        private DailyScheduleFormatterInterface $dailyScheduleFormatter,
        private PoiProviderInterface $poiProvider,
    ) {
    }

    public function buildPlan(PlanContext $context): TripPlan
    {
        $trip = new TripPlan();
        $trip->name = $context->planName;
        $trip->currency = $context->currency;
        $trip->totalBudget = $context->totalBudget;
        $trip->totalDuration = $context->totalDuration;
        $trip->startDate = $context->startDate;
        $trip->endDate = $context->endDate;

        $currentDate = $context->startDate;
        foreach ($context->stops as $stop) {
            $stopPlan = $this->stopPlanGenerator->generate($stop);

            $stopPlan->startDate = $currentDate;
            $stopPlan->endDate = $currentDate->modify('+' . ($stop->duration - 1) . ' days');
            $currentDate = $stopPlan->endDate->modify('+1 day');

            // 🔁 після того як є дати — формуємо день за днем
            $activities = $this->poiProvider->getActivities($stop->cityName, $stop->interests);
            $foodPlaces = $this->poiProvider->getFoodPlaces($stop->cityName);
            $stopPlan->days = $this->dailyScheduleFormatter->format($stopPlan, $activities, $foodPlaces);

            $trip->stops[] = $stopPlan;
        }

        return $trip;
    }
}
