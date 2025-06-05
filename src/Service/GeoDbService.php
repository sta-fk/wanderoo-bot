<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoDbService
{
    private const BASE_URL = 'https://wft-geo-db.p.rapidapi.com/v1/geo';

    private string $geoDbApiKey;
    private HttpClientInterface $client;

    public function __construct(
        ParameterBagInterface $params,
        HttpClientInterface $client,
    ) {
        $this->geoDbApiKey = $params->get('geodb_api_key');
        $this->client = $client;
    }

    private function request(string $endpoint, array $query = []): array
    {
        $response = $this->client->request('GET', self::BASE_URL.$endpoint, [
            'headers' => [
                'X-RapidAPI-Key' => $this->geoDbApiKey,
                'X-RapidAPI-Host' => 'wft-geo-db.p.rapidapi.com',
            ],
            'query' => $query,
        ]);

        $data = $response->toArray(false); // false — щоб уникнути винятків при 4xx

        return $data['data'] ?? [];
    }

    public function getContinents(): array
    {
        return $this->request('/continents');
    }

    public function getCountriesByContinent(string $continentCode): array
    {
        return $this->request('/countries', [
            'continentCode' => $continentCode,
            'limit' => 10,
            'sort' => 'name',
        ]);
    }

    public function getCitiesByCountry(string $countryCode): array
    {
        return $this->request('/cities', [
            'countryIds' => $countryCode,
            'sort' => 'population',
            'limit' => 10,
        ]);
    }
}
