<?php

namespace App\Service\TripPlanner;

use App\DTO\DayPlan;
use App\DTO\StopContext;
use App\DTO\StopPlan;

readonly class DefaultStopPlanGenerator implements StopPlanGeneratorInterface
{
    public function __construct(
        private PoiProviderInterface $poiProvider,
        private TransportProviderInterface $transportProvider,
        private DailyScheduleFormatterInterface $dailyScheduleFormatter
    ) {
    }

    public function generate(StopContext $stop): StopPlan
    {
        $activities = $this->poiProvider->getActivities($stop->cityName, $stop->interests);
        $foodPlaces = $this->poiProvider->getFoodPlaces($stop->cityName);
        $transportInfo = $this->transportProvider->getLocalTransportInfo($stop->cityName);

        $stopPlan = new StopPlan();
        $stopPlan->cityName = $stop->cityName;
        $stopPlan->countryName = $stop->countryName;
        $stopPlan->localTransportInfo = $transportInfo;

        $stopPlan->days = $this->dailyScheduleFormatter->format(
            $stop,
            $activities,
            $foodPlaces
        );

        return $stopPlan;
    }
}
