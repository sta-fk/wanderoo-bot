<?php

namespace App\DTO\Internal\InitialStopFlowViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class CitySearchResultViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public array $cities = [],
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CitySearchResult;
    }
}
