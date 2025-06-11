<?php

namespace App\Service\Integrations;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CurrencyExchangerService
{    private const EXCHANGE_API_URL = 'https://api.exchangerate.host/latest';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly int $exchangerTtl,
    ) {}

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if (strtoupper($fromCurrency) === strtoupper($toCurrency)) {
            return $amount;
        }

        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);
        return round($amount * $rate, 2);
    }

    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}";

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($fromCurrency, $toCurrency) {
            $item->expiresAfter($this->exchangerTtl);

            $response = $this->httpClient->request('GET', self::EXCHANGE_API_URL, [
                'query' => [
                    'base' => $fromCurrency,
                    'symbols' => $toCurrency,
                ]
            ]);

            $data = $response->toArray();
            return $data['rates'][$toCurrency] ?? throw new \RuntimeException("Exchange rate $fromCurrency → $toCurrency not found");
        });
    }
}
