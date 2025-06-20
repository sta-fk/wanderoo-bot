<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CountryInputSearchResultViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;

readonly class CountryInputSearchViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->message;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCountryInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->message->chat->id;
        $countries = $this->placeService->searchCountries($update->message->text);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new CountryInputSearchResultViewData($chatId, $countries),
            States::WaitingForCountryPicked
        );
    }
}
