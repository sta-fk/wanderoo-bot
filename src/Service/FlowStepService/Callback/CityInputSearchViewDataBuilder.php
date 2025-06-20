<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CityInputSearchResultViewData;
use App\DTO\Internal\CountryInputSearchResultViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CityInputSearchViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->message;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCityInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);
        $countryCode = $context->currentStopDraft?->countryCode ?? null;
        $cities = $this->placeService->searchCities($update->message->text, $countryCode);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new CityInputSearchResultViewData($chatId, $cities),
            States::WaitingForCityPicked,
        );
    }
}
