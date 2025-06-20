<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\AddStopViewData;
use App\DTO\Internal\CityInputViewData;
use App\DTO\Internal\CountryInputViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\CurrencyResolverService;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\FlowStepService\ViewDataBuilderInterface;
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
        return null !== $update->callbackQuery
            && (CallbackQueryData::StopCountryAnother->value === $update->callbackQuery->data
                || CallbackQueryData::StopCountrySame->value === $update->callbackQuery->data);
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
            new CountryInputViewData($update->callbackQuery->message->chat->id),
            States::WaitingForCountryInput
        );
    }

    private function buildWithSameCountry(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $countryPlaceId = ($context->stops[count($context->stops) - 1])->countryPlaceId;

        $countryDetails = $this->placeService->getPlaceDetails($countryPlaceId);

        $context->currentStopDraft->countryName = $countryDetails->name;
        $context->currentStopDraft->countryCode = $countryDetails->countryCode;
        $context->currentStopDraft->countryPlaceId = $countryPlaceId;
        $context->currentStopDraft->currency = $this->currencyResolverService->resolveCurrencyCode($countryDetails->countryCode);

        $this->userStateStorage->saveContext($chatId, $context);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new CityInputViewData($update->callbackQuery->message->chat->id),
            States::WaitingForCityInput
        );
    }
}
