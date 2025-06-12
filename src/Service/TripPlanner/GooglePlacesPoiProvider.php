<?php

namespace App\Service\TripPlanner;

use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class GooglePlacesPoiProvider implements PoiProviderInterface
{
    private const PLACES_SEARCH_URL = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';

    public function __construct(
        private HttpClientInterface $client,
        private string $apiKey,
        private string $language = 'uk'
    ) {
    }

    public function getActivities(string $city, array $interests): array
    {
        $coords = $this->getCityCoordinates($city);
        if (!$coords) {
            return [];
        }

        $results = [];
        foreach ($this->mapInterestsToTypes($interests) as $type) {
            $resp = $this->client->request(
                'GET',
                self::PLACES_SEARCH_URL,
                [
                'query' => [
                    'location' => "{$coords['lat']},{$coords['lng']}",
                    'radius' => 5000,
                    'type' => $type,
                    'key' => $this->apiKey,
                    'language' => $this->language,
                ],
                ]
            );

            foreach (($resp->toArray()['results'] ?? []) as $place) {
                $results[] = $place['name'];
                if (count($results) >= 5) {
                    break 2;
                }
            }
        }

        return $results;
    }

    public function getFoodPlaces(string $city): array
    {
        $coords = $this->getCityCoordinates($city);
        if (!$coords) {
            return [];
        }

        $resp = $this->client->request(
            'GET',
            self::PLACES_SEARCH_URL,
            [
            'query' => [
                'location' => "{$coords['lat']},{$coords['lng']}",
                'radius' => 5000,
                'type' => 'restaurant',
                'key' => $this->apiKey,
                'language' => $this->language,
            ],
            ]
        );

        return array_slice(
            array_map(fn ($p) => $p['name'], $resp->toArray()['results'] ?? []),
            0,
            5
        );
    }

    private function getCityCoordinates(string $city): ?array
    {
        $resp = $this->client->request(
            'GET',
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
            'query' => [
                'address' => $city,
                'key' => $this->apiKey,
                'language' => $this->language,
            ],
            ]
        );

        $data = $resp->toArray();
        if (empty($data['results'][0]['geometry']['location'])) {
            return null;
        }

        return $data['results'][0]['geometry']['location'];
    }

    private function mapInterestsToTypes(array $interests): array
    {
        $map = [
            'city' => 'tourist_attraction',
            'nature' => 'park',
            'culture' => 'museum',
            'shopping' => 'shopping_mall',
            'beach' => 'beach',
            'food' => 'food',
        ];

        return array_map(fn ($i) => $map[$i] ?? 'point_of_interest', $interests);
    }
}
