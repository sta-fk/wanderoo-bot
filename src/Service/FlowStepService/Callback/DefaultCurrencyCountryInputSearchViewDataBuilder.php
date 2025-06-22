<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\DefaultCurrencyCountryInputSearchResultViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;

readonly class DefaultCurrencyCountryInputSearchViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->isMessageUpdate();
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $countries = $this->placeService->searchCountries($update->message->text);

        return ViewDataCollection::createWithSingleViewData(
            new DefaultCurrencyCountryInputSearchResultViewData($update->getChatId(), $countries),
        );
    }
}
