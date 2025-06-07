<?php

namespace App\Service\KeyboardService;

use App\DTO\Keyboard;
use App\Enum\CallbackQueryData;
use App\Service\GeoDbService;

class CountryKeyboardProvider
{
    use BuildPaginationKeyboardTrait;

    private const KEYBOARD_TEXT_FIELD = 'name';
    private const KEYBOARD_KEY_FIELD = 'code';

    public function __construct(private readonly GeoDbService $geoDbService){}

    public function provideDefaultKeyboard(): array
    {
        return $this->getKeyboard(self::DEFAULT_PAGINATION_LIMIT);
    }

    public function providePaginationKeyboard(int $nextPageOffset): array
    {
        $nextPageOffset += self::DEFAULT_PAGINATION_LIMIT;

        return $this->getKeyboard($nextPageOffset);
    }

    private function getKeyboard(int $nextPageOffset): array
    {
        $countries = $this->geoDbService->getCountries();

        return $this->buildPaginationKeyboard(
            new Keyboard(
                $countries,
                CallbackQueryData::Country->value,
                self::KEYBOARD_TEXT_FIELD,
                self::KEYBOARD_KEY_FIELD,
                CallbackQueryData::CountryPage->value,
                $nextPageOffset
            ),
        );
    }
}
