<?php

namespace App\Service\TripPlanner;

use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class GoogleTransitTransportProvider implements TransportProviderInterface
{
    private const GOOGLE_SEARCH_API = 'https://www.googleapis.com/customsearch/v1';
    private const QUERY_TEMPLATE = 'Public transport in %s city';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
        private string $searchEngineId, // CX parameter from Custom Search
        private string $language = 'uk'
    ) {
    }

    public function getLocalTransportInfo(string $city): ?string
    {
        $query = sprintf(self::QUERY_TEMPLATE, $city);

        $response = $this->httpClient->request(
            'GET',
            self::GOOGLE_SEARCH_API,
            [
            'query' => [
                'key' => $this->apiKey,
                'cx' => $this->searchEngineId,
                'q' => $query,
                'lr' => $this->getLanguageRestriction(),
                'hl' => $this->language,
                'num' => 1,
            ],
            ]
        );

        $data = $response->toArray(false);

        if (!empty($data['items'][0]['snippet'])) {
            return $data['items'][0]['snippet'];
        }

        return null;
    }

    private function getLanguageRestriction(): string
    {
        return match ($this->language) {
            'uk' => 'lang_uk',
            'en' => 'lang_en',
            default => '',
        };
    }
}
