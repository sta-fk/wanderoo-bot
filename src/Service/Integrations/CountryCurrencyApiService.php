<?php

namespace App\Service\Integrations;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class CountryCurrencyApiService
{
    private const REST_COUNTRIES_URL = 'https://restcountries.com/v3.1/alpha/';

    public function __construct(
        private CacheInterface $cache,
        private HttpClientInterface $httpClient,
    ) {
    }

    public function getCurrencyCode(string $countryCode): ?string
    {
        $countryCode = strtoupper($countryCode);

        return $this->cache->get(
            'currency_' . $countryCode,
            function () use ($countryCode) {
                $response = $this->httpClient->request('GET', self::REST_COUNTRIES_URL . $countryCode);
                $data = $response->toArray();

                if (empty($data) || !isset($data[0]['currencies'])) {
                    return null;
                }

                // currencies is an object with currencyCode as key (e.g. "EUR")
                $currencies = $data[0]['currencies'];
                $currencyCodes = array_keys($currencies);

                return $currencyCodes[0] ?? null;
            }
        );
    }
}
