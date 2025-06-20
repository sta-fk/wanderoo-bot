<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class CountryInputSearchResultViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public array $countries = [],
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CountryInputSearchResult;
    }
}
