<?php

namespace App\DTO;

class GooglePlace
{
    public function __construct(
        public readonly string $placeId,
        public readonly string $name,
        public readonly string $countryCode,
    ) {
    }
}
