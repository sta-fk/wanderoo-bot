<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class CityPickedViewData implements ViewDataInterface
{
    public function __construct(
        public int $callbackQueryId,
        public string $chosenCityName,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CityPicked;
    }
}
