<?php

namespace App\Service\ViewDataBuilder\Callback\MenuActions\SettingsActions;

use App\DTO\Internal\MenuActionsViewData\SettingsActionsViewData\DefaultCurrencyCountryInputSearchResultViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;

readonly class DefaultCurrencyCountryInputSearchViewDataBuilder implements StateAwareViewDataBuilderInterface
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
        return [States::WaitingForDefaultCurrencyCountryInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $countries = $this->placeService->searchCountries($update->message->text);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new DefaultCurrencyCountryInputSearchResultViewData($update->getChatId(), $countries),
            States::WaitingForDefaultCurrencyPicked
        );
    }
}
