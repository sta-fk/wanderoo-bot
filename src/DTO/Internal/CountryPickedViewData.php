<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class CountryPickedViewData implements ViewDataInterface
{
    public function __construct(
        public int $callbackQueryId,
        public string $chosenCountryName,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::CountryPicked;
    }
}
