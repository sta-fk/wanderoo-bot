<?php

namespace App\Service\Integrations;

use App\DTO\GooglePlace;
use App\DTO\GooglePlaceDetails;

interface PlaceServiceInterface
{
    /** @return GooglePlace[] */
    public function searchCountries(string $query): array;

    /** @return GooglePlace[] */
    public function searchCities(string $query, ?string $countryCode = null): array;

    public function getPlaceDetails(string $placeId): GooglePlaceDetails;
}
