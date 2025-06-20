<?php

namespace App\DTO\Internal;

use App\Enum\States;
use App\Enum\MessageView;

readonly class CityInputSearchResultViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public array $cities = [],
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CityInputSearchResult;
    }
}
