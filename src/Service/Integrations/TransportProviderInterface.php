<?php

namespace App\Service\Integrations;

interface TransportProviderInterface
{
    public function getLocalTransportInfo(string $city): ?string;
}
