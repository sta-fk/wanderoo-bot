<?php

namespace App\Enum;

enum SupportedLanguages: string
{
    case Ukrainian = 'uk';
    case English = 'en';

    public static function fromExternalLocale(string $externalLocale): SupportedLanguages
    {
        $locale = self::tryFrom(strtolower($externalLocale));
        return $locale ?? self::English;
    }
}
