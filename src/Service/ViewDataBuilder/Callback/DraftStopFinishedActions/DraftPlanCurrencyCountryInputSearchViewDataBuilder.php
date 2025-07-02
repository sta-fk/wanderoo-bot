<?php

namespace App\Service\ViewDataBuilder\Callback\DraftStopFinishedActions;

use App\DTO\Internal\TripStopGenerationFinishedViewData\DraftPlanCurrencyCountryInputSearchResultViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;

readonly class DraftPlanCurrencyCountryInputSearchViewDataBuilder implements StateAwareViewDataBuilderInterface
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
        return [States::WaitingForDraftPlanCurrencyCountryInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $countries = $this->placeService->searchCountries($update->message->text);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new DraftPlanCurrencyCountryInputSearchResultViewData($update->getChatId(), $countries),
            States::WaitingForDraftPlanCurrencyPicked
        );
    }
}
