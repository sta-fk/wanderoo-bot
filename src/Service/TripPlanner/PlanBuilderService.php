<?php

namespace App\Service\TripPlanner;

use App\DTO\PlanContext;
use App\DTO\TripPlan;

readonly class PlanBuilderService
{
    public function __construct(
        private StopPlanGeneratorInterface $stopPlanGenerator
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

            $trip->stops[] = $stopPlan;
        }

        return $trip;
    }
}
