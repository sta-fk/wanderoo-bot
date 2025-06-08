<?php

namespace App\Service\KeyboardService;

use App\DTO\Keyboard;
use App\Enum\CallbackQueryData;
use App\Service\GeoDbService;

class StopCountryKeyboardProvider
{
    use BuildPaginationKeyboardTrait;

    private const KEYBOARD_TEXT_FIELD = 'name';
    private const KEYBOARD_KEY_FIELD = 'code';

    public function __construct(private readonly GeoDbService $geoDbService)
    {
    }

    public function provideDefaultKeyboard(): array
    {
        return $this->getKeyboard();
    }

    public function providePaginationKeyboard(int $nextPageOffset): array
    {
        return $this->getKeyboard($nextPageOffset);
    }

    private function getKeyboard(int $nextPageOffset = 0): array
    {
        $countries = $this->geoDbService->getCountries($nextPageOffset, self::DEFAULT_PAGINATION_LIMIT);

        return $this->buildPaginationKeyboard(
            new Keyboard(
                $countries,
                CallbackQueryData::StopCountry->value,
                self::KEYBOARD_TEXT_FIELD,
                self::KEYBOARD_KEY_FIELD,
                CallbackQueryData::StopCountryPage->value,
                $nextPageOffset + self::DEFAULT_PAGINATION_LIMIT
            ),
        );
    }
}
