<?php

namespace App\DTO;

use App\Service\FlowStepService\StartFlowStepService\InterestsService;
use App\Service\FlowStepService\StartFlowStepService\TripStyleService;

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
            static fn ($key) => strtolower(InterestsService::INTERESTS[$key]) ?? $key,
            $this->interests ?? []
        );
    }

    public function getTripStyleLabel(): string
    {
        return TripStyleService::TRIP_STYLE_OPTIONS[$this->tripStyle] ?? '???';
    }
}
