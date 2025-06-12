<?php

namespace App\Service\Integrations;

interface PoiProviderInterface
{
    public function getActivities(string $city, array $interests): array;
    public function getFoodPlaces(string $city): array;
}
