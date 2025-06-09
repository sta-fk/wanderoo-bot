<?php

namespace App\DTO;

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
    public ?string $budget = null;
}
