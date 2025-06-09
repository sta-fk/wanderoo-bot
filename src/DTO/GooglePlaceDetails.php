<?php

namespace App\DTO;

class GooglePlaceDetails
{
    public function __construct(
        public readonly string $placeId,
        public readonly string $name,
        public readonly string $countryCode,
        public readonly float $lat,
        public readonly float $lng,
    ) {
    }
}
