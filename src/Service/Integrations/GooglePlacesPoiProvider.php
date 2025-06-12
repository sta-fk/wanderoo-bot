<?php

namespace App\Service\Integrations;

use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class GooglePlacesPoiProvider implements PoiProviderInterface
{
    private const PLACES_SEARCH_URL = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';

    public function __construct(
        private HttpClientInterface $client,
        private string $apiKey,
        private string $language = 'uk',
        private int $defaultRadius = 7000,
    ) {
    }

    public function getActivities(string $city, array $interests, int $limit = 20): array
    {
        $coords = $this->getCityCoordinates($city);
        if (!$coords) {
            return [];
        }

        $results = [];
        foreach ($this->mapInterestsToTypes($interests) as $type) {
            $pageToken = null;

            do {
                $response = $this->client->request('GET', self::PLACES_SEARCH_URL, [
                    'query' => array_filter([
                        'location' => "{$coords['lat']},{$coords['lng']}",
                        'radius' => $this->defaultRadius,
                        'type' => $type,
                        'key' => $this->apiKey,
                        'language' => $this->language,
                        'pagetoken' => $pageToken,
                    ]),
                ]);

                $data = $response->toArray(false);
                foreach ($data['results'] ?? [] as $place) {
                    // Фільтр: лише популярні та відкриті місця з рейтингом
                    if (($place['user_ratings_total'] ?? 0) >= 50) {
                        $results[] = $place['name'];
                    }

                    if (count($results) >= $limit) {
                        break 2;
                    }
                }

                $pageToken = $data['next_page_token'] ?? null;
                if ($pageToken) {
                    sleep(2); // Google вимагає затримку перед наступним токеном
                }
            } while ($pageToken && count($results) < $limit);
        }

        return array_unique($results);
    }

    public function getFoodPlaces(string $city, int $limit = 15): array
    {
        $coords = $this->getCityCoordinates($city);
        if (!$coords) {
            return [];
        }

        $response = $this->client->request('GET', self::PLACES_SEARCH_URL, [
            'query' => [
                'location' => "{$coords['lat']},{$coords['lng']}",
                'radius' => $this->defaultRadius,
                'type' => 'restaurant',
                'key' => $this->apiKey,
                'language' => $this->language,
            ],
        ]);

        $data = $response->toArray(false);
        $places = array_filter($data['results'] ?? [], fn ($p) => ($p['user_ratings_total'] ?? 0) >= 50);

        return array_slice(array_map(fn ($p) => $p['name'], $places), 0, $limit);
    }

    private function getCityCoordinates(string $city): ?array
    {
        $resp = $this->client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => $city,
                'key' => $this->apiKey,
                'language' => $this->language,
            ],
        ]);

        $data = $resp->toArray(false);
        return $data['results'][0]['geometry']['location'] ?? null;
    }

    private function mapInterestsToTypes(array $interests): array
    {
        $map = [
            'city' => ['tourist_attraction', 'point_of_interest'],
            'nature' => ['park', 'natural_feature'],
            'culture' => ['museum', 'art_gallery', 'church'],
            'shopping' => ['shopping_mall', 'store'],
            'beach' => ['beach'],
            'food' => ['bakery', 'cafe', 'restaurant'],
        ];

        $types = [];
        foreach ($interests as $interest) {
            $points = $map[$interest] ?? ['point_of_interest'];
            $types = array_merge($types, $points);
        }

        return array_unique($types);
    }
}
