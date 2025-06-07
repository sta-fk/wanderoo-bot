<?php

namespace App\Service\KeyboardService;

use App\DTO\Keyboard;
use App\Enum\CallbackQueryData;
use App\Service\GeoDbService;

class CityKeyboardProvider
{
    use BuildPaginationKeyboardTrait;

    private const KEYBOARD_TEXT_FIELD = 'name';
    private const KEYBOARD_KEY_FIELD = 'name';

    public function __construct(private readonly GeoDbService $geoDbService){}

    public function provideDefaultKeyboard(string $countryCode): array
    {
        $cities = $this->geoDbService->getCitiesByCountry($countryCode);

        return $this->getKeyboard($cities, self::DEFAULT_PAGINATION_LIMIT);
    }

    public function providePaginationKeyboard(string $countryCode, int $nextPageOffset): array
    {
        $cities = $this->geoDbService->getCitiesByCountry($countryCode, $nextPageOffset);
        $nextPageOffset += self::DEFAULT_PAGINATION_LIMIT;

        return $this->getKeyboard($cities, $nextPageOffset);
    }

    private function getKeyboard(array $cities, int $nextPageOffset): array
    {
        return $this->buildPaginationKeyboard(
            new Keyboard(
                $cities,
                CallbackQueryData::City->value,
                self::KEYBOARD_TEXT_FIELD,
                self::KEYBOARD_KEY_FIELD,
                CallbackQueryData::CityPage->value,
                $nextPageOffset
            ),
        );
    }
}
