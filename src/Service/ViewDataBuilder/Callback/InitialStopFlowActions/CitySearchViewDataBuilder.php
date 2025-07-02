<?php

namespace App\Service\ViewDataBuilder\Callback\InitialStopFlowActions;

use App\DTO\Internal\InitialStopFlowViewData\CitySearchResultViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CitySearchViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->isMessageUpdate();
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCityInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);
        $countryCode = $context->currentStopDraft?->countryCode ?? null;
        $cities = $this->placeService->searchCities($update->message->text, $countryCode);
        $nextState = empty($cities) ? States::WaitingForCityInput : States::WaitingForCityPicked;

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new CitySearchResultViewData($chatId, $cities),
            $nextState,
        );
    }
}
