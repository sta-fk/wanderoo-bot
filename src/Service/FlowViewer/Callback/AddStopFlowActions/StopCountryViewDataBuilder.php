<?php

namespace App\Service\FlowViewer\Callback\AddStopFlowActions;

use App\DTO\Internal\CityInputViewData;
use App\DTO\Internal\CountryInputViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\CurrencyResolverService;
use App\Service\FlowViewer\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class StopCountryViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlaceServiceInterface $placeService,
        private CurrencyResolverService $currencyResolverService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::StopCountryAnother)
            || $update->supportsCallbackQuery(CallbackQueryData::StopCountrySame);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStopCountry];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        if (CallbackQueryData::StopCountryAnother->value === $update->callbackQuery->data) {
            return $this->buildWithAnotherCountry($update);
        }

        return $this->buildWithSameCountry($update);
    }

    private function buildWithAnotherCountry(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createStateAwareWithSingleViewData(
            new CountryInputViewData($update->getChatId()),
            States::WaitingForCountryInput
        );
    }

    private function buildWithSameCountry(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $countryPlaceId = $context->getLastSavedStop()->countryPlaceId;

        $countryDetails = $this->placeService->getPlaceDetails($countryPlaceId);

        $context->currentStopDraft->countryName = $countryDetails->name;
        $context->currentStopDraft->countryCode = $countryDetails->countryCode;
        $context->currentStopDraft->countryPlaceId = $countryPlaceId;
        $context->currentStopDraft->currency = $this->currencyResolverService->resolveCurrencyCode($countryDetails->countryCode);

        $this->userStateStorage->saveContext($chatId, $context);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new CityInputViewData($chatId),
            States::WaitingForCityInput
        );
    }
}
