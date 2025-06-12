<?php

namespace App\Service\TripPlanner;

interface TransportProviderInterface
{
    public function getLocalTransportInfo(string $city): ?string;
}
