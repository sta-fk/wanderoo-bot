<?php

namespace App\Service\Integrations;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheableGoogleTransitTransportProvider implements TransportProviderInterface
{
    private TransportProviderInterface $inner;
    private CacheInterface $cache;
    private int $googleTransitTransportTtl;

    public function __construct(TransportProviderInterface $inner, CacheInterface $cache, int $googleTransitTransportTtl)
    {
        $this->inner = $inner;
        $this->cache = $cache;
        $this->googleTransitTransportTtl = $googleTransitTransportTtl;
    }

    public function getLocalTransportInfo(string $city): ?string
    {
        $cacheKey = $this->makeKey($city);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($city) {
            $item->expiresAfter($this->googleTransitTransportTtl);
            return $this->inner->getLocalTransportInfo($city);
        });
    }

    private function makeKey(string $city): string
    {
        $citySlug = mb_strtolower(trim($city));
        return sprintf('transport_%s', $citySlug);
    }
}
