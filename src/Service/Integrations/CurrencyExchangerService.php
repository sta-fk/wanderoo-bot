<?php

namespace App\Service\Integrations;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class CurrencyExchangerService
{
    private const EXCHANGE_API_URL = 'https://open.er-api.com/v6/latest/';

    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
        private int $exchangerTtl,
    ) {
    }

    public function getExchangeRate(string $from, string $to): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return 1.0;
        }

        $cacheKey = sprintf('exchange_rates_%s', $from);

        $rates = $this->cache->get(
            $cacheKey,
            function (ItemInterface $item) use ($from) {
                $item->expiresAfter($this->exchangerTtl);

                $response = $this->httpClient->request('GET', self::EXCHANGE_API_URL . $from);

                $data = $response->toArray();

                if (!isset($data['rates']) || !is_array($data['rates'])) {
                    throw new \RuntimeException("Invalid exchange rate response for base currency: {$from}");
                }

                return $data['rates'];
            }
        );

        if (!isset($rates[$to])) {
            throw new \RuntimeException("Exchange rate from {$from} to {$to} not found.");
        }

        return (float) $rates[$to];
    }

    public function convert(float $amount, string $from, string $to): float
    {
        $rate = $this->getExchangeRate($from, $to);
        return round($amount * $rate, 2);
    }

    public function convertToMultiple(string $fromCurrency, array $toCurrencies, float $amount): array
    {
        $results = [];

        foreach (array_unique($toCurrencies) as $targetCurrency) {
            if (strtoupper($targetCurrency) === strtoupper($fromCurrency)) {
                $results[$targetCurrency] = $amount;
                continue;
            }

            $rate = $this->getExchangeRate($fromCurrency, $targetCurrency);
            $results[$targetCurrency] = round($amount * $rate, 2);
        }

        return $results;
    }
}
