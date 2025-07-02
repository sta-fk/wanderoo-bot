<?php

namespace App\Service\ViewDataBuilder\Callback\InitialStopFlowActions;

use App\DTO\Internal\InitialStopFlowViewData\CountrySearchResultViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;

readonly class CountrySearchViewDataBuilder implements StateAwareViewDataBuilderInterface
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
        return [States::WaitingForCountryInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $countries = $this->placeService->searchCountries($update->message->text);
        $nextState = empty($countries) ? States::WaitingForCountryInput : States::WaitingForCountryPicked;

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new CountrySearchResultViewData($update->getChatId(), $countries),
            $nextState
        );
    }
}
