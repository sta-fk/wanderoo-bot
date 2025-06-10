<?php

namespace App\Service;

use App\Service\Integrations\CountryCurrencyApiService;

class CurrencyResolverService
{
    private const COUNTRY_TO_CURRENCY = [
        'US' => 'USD',
        'GB' => 'GBP',
        'FR' => 'EUR',
        'DE' => 'EUR',
        'IT' => 'EUR',
        'ES' => 'EUR',
        'PT' => 'EUR',
        'UA' => 'UAH',
        'PL' => 'PLN',
        'CH' => 'CHF',
        'CA' => 'CAD',
        'AU' => 'AUD',
        'JP' => 'JPY',
        'CN' => 'CNY',
    ];

    public function __construct(
        private readonly CountryCurrencyApiService $countryCurrencyApiService,
    ) {}

    public function resolveCurrencyCode(string $countryCode): ?string
    {
        $countryCode = strtoupper($countryCode);

        return self::COUNTRY_TO_CURRENCY[$countryCode] ?? $this->countryCurrencyApiService->getCurrencyCode($countryCode);
    }
}
