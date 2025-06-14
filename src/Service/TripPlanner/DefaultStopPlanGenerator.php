<?php

namespace App\Service\TripPlanner;

use App\DTO\StopContext;
use App\DTO\StopPlan;
use App\Service\Integrations\TransportProviderInterface;

readonly class DefaultStopPlanGenerator implements StopPlanGeneratorInterface
{
    public function __construct(
        private TransportProviderInterface $transportProvider
    ) {
    }

    public function generate(StopContext $stop): StopPlan
    {
        $transportInfo = $this->transportProvider->getLocalTransportInfo($stop->cityName);

        $stopPlan = new StopPlan();
        $stopPlan->cityName = $stop->cityName;
        $stopPlan->countryName = $stop->countryName;
        $stopPlan->localTransportInfo = $transportInfo;
        $stopPlan->currency = $stop->currency;
        $stopPlan->budget = $stop->budget;
        $stopPlan->tripStyle = $stop->tripStyle;

        // Дати та дні будуть призначені у PlanBuilderService
        $stopPlan->days = [];

        return $stopPlan;
    }
}
