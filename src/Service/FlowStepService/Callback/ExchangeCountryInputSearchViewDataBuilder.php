<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CountryInputSearchResultViewData;
use App\DTO\Internal\ExchangeCountryInputSearchResultViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;

readonly class ExchangeCountryInputSearchViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->isMessageUpdate();
    }

    public function supportsStates(): array
    {
        return [States::WaitingForExchangeCountryInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $countries = $this->placeService->searchCountries($update->message->text);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new ExchangeCountryInputSearchResultViewData($update->getChatId(), $countries),
            States::WaitingForExchangePicked
        );
    }
}
