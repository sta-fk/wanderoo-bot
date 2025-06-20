<?php

namespace App\DTO\TripPlan;

use App\DTO\TripPlan\DayPlan;

class StopPlan
{
    public ?string $countryName = null;
    public ?string $cityName = null;
    public ?int $duration = null;
    public ?\DateTimeImmutable $startDate = null;
    public ?\DateTimeImmutable $endDate = null;
    public ?float $budget = null;
    public ?string $currency = null;
    public ?string $tripStyle = null;
    public array $interests = [];

    public ?string $localTransportInfo = null;

    /** @var DayPlan[] */
    public array $days = [];
}
