<?php

namespace App\Service\Integrations;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheableGooglePlacesPoiProvider implements PoiProviderInterface
{
    private PoiProviderInterface $inner;
    private CacheInterface $cache;
    private int $googlePlacesPOITtl;

    public function __construct(PoiProviderInterface $inner, CacheInterface $cache, int $googlePlacesPOITtl)
    {
        $this->inner = $inner;
        $this->cache = $cache;
        $this->googlePlacesPOITtl = $googlePlacesPOITtl;
    }

    public function getActivities(string $city, array $interests): array
    {
        $cacheKey = $this->makeKey('activities', $city, $interests);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($city, $interests) {
            $item->expiresAfter($this->googlePlacesPOITtl);
            return $this->inner->getActivities($city, $interests);
        });
    }

    public function getFoodPlaces(string $city): array
    {
        $cacheKey = $this->makeKey('food', $city);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($city) {
            $item->expiresAfter($this->googlePlacesPOITtl);
            return $this->inner->getFoodPlaces($city);
        });
    }

    private function makeKey(string $type, string $city, array $interests = []): string
    {
        $citySlug = mb_strtolower(trim($city));
        $interestsKey = $interests ? implode(',', array_map('mb_strtolower', $interests)) : 'none';
        return sprintf('poi_%s_%s_%s', $type, $citySlug, md5($interestsKey));
    }
}
