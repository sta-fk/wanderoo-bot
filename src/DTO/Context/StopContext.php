<?php

namespace App\DTO\Context;

use App\Service\Draft\FlowStepService\StartFlowStepService\InterestsService;
use App\Service\Draft\FlowStepService\StartFlowStepService\TripStyleService;
use App\Service\FlowViewer\TelegramView\InitialStopFlowViewer\InterestsViewer;
use App\Service\FlowViewer\TelegramView\InitialStopFlowViewer\TripStyleViewer;

class StopContext
{
    public ?string $countryPlaceId = null;
    public ?string $countryName = null;
    public ?string $countryCode = null;

    public ?string $cityPlaceId = null;
    public ?string $cityName = null;
    public ?float $lat = null;
    public ?float $lng = null;

    public ?int $duration = null;
    public ?string $tripStyle = null;
    public array $interests = [];
    public ?string $currency = null;
    public ?string $budgetInPlanCurrency = null;

    public ?string $budget = null;

    public function getInterestsLabels(): array
    {
        return array_map(
            static fn ($key) => strtolower(InterestsViewer::INTERESTS[$key]) ?? $key,
            $this->interests ?? []
        );
    }

    public function getTripStyleLabel(): string
    {
        return TripStyleViewer::TRIP_STYLE_OPTIONS[$this->tripStyle] ?? '???';
    }
}
