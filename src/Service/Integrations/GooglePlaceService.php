<?php

namespace App\Service\Integrations;

use App\DTO\GooglePlace;
use App\DTO\GooglePlaceDetails;
use App\Service\Integrations\PlaceServiceInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GooglePlaceService implements PlaceServiceInterface
{
    private const AUTOCOMPLETE_URL = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
    private const DETAILS_URL = 'https://maps.googleapis.com/maps/api/place/details/json';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $googleApiKey,
    ) {
    }

    public function searchCountries(string $query): array
    {
        return $this->autocomplete($query, 'country');
    }

    public function searchCities(string $query, ?string $countryCode = null): array
    {
        return $this->autocomplete($query, 'locality', $countryCode);
    }

    public function getPlaceDetails(string $placeId): GooglePlaceDetails
    {
        $response = $this->httpClient->request(
            'GET',
            self::DETAILS_URL,
            [
            'query' => [
                'place_id' => $placeId,
                'key' => $this->googleApiKey,
                'fields' => 'place_id,name,geometry,adr_address,address_components',
                'language' => 'uk',
            ],
            ]
        );

        $data = $response->toArray();

        $result = $data['result'] ?? null;
        if (!$result) {
            throw new \RuntimeException('Place details not found');
        }

        $countryCode = '';
        foreach ($result['address_components'] as $component) {
            if (in_array('country', $component['types'], true)) {
                $countryCode = $component['short_name'];
                break;
            }
        }

        return new GooglePlaceDetails(
            $result['place_id'],
            $result['name'],
            $countryCode,
            $result['geometry']['location']['lat'],
            $result['geometry']['location']['lng'],
        );
    }

    private function autocomplete(string $query, string $type, ?string $countryCode = null): array
    {
        $params = [
            'input' => $query,
            'key' => $this->googleApiKey,
            'types' => '(regions)', // по факту впливає тільки на countries + regions
            'language' => 'uk',
        ];

        if ($type === 'country') {
            $params['types'] = '(regions)'; // обхідний спосіб для countries
        } elseif ($type === 'locality') {
            $params['types'] = '(cities)';
        }

        if ($countryCode) {
            $params['components'] = 'country:' . strtolower($countryCode);
        }

        $response = $this->httpClient->request(
            'GET',
            self::AUTOCOMPLETE_URL,
            [
            'query' => $params,
            ]
        );

        $data = $response->toArray();

        $places = [];
        foreach ($data['predictions'] as $prediction) {
            $countryCodeFromTerms = '';
            if (!empty($prediction['terms'])) {
                $lastTerm = end($prediction['terms']);
                $countryCodeFromTerms = $lastTerm['value'] ?? '';
            }

            $places[] = new GooglePlace(
                $prediction['place_id'],
                $prediction['description'],
                $countryCodeFromTerms
            );
        }

        return $places;
    }
}
