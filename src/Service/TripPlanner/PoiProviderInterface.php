<?php

namespace App\Service\TripPlanner;

interface PoiProviderInterface
{
    public function getActivities(string $city, array $interests): array;
    public function getFoodPlaces(string $city): array;
}
