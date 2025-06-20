<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CityInputViewData;
use App\DTO\Internal\CountryPickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\CurrencyResolverService;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CountryPickedViewDataBuilder implements StateAwareViewDataBuilderInterface
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
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Country->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCountryPicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $countryPlaceId = substr($update->callbackQuery->data, strlen(CallbackQueryData::Country->value));

        $countryDetails = $this->placeService->getPlaceDetails($countryPlaceId);
        $countryCurrency = $this->currencyResolverService->resolveCurrencyCode($countryDetails->countryCode);

        $context->currentStopDraft->countryName = $countryDetails->name;
        $context->currentStopDraft->countryCode = $countryDetails->countryCode;
        $context->currentStopDraft->countryPlaceId = $countryPlaceId;
        $context->currentStopDraft->currency = $countryCurrency;

        // !! Тільки для ПЕРШОГО степу встановити валюту країни перебування
        if (!$context->isAddingStopFlow) {
            $context->currency = $countryCurrency;
        }

        $this->userStateStorage->saveContext($chatId, $context);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection->add(new CountryPickedViewData($chatId, $update->callbackQuery->id));
        $viewDataCollection->add(new CityInputViewData($chatId));
        $viewDataCollection->setNextState(States::WaitingForCityInput);

        return $viewDataCollection;
    }
}
