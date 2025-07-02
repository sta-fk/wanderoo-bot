<?php

namespace App\Service\ViewDataBuilder\Callback\InitialStopFlowActions;

use App\DTO\Internal\InitialStopFlowViewData\CityInputViewData;
use App\DTO\Internal\InitialStopFlowViewData\CountryPickedViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\CurrencyResolverService;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
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
        return $update->supportsCallbackQuery(CallbackQueryData::Country);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCountryPicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $this->processCountryPicked($update);

        $context = $this->userStateStorage->getContext($update->getChatId());
        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add(new CountryPickedViewData($update->callbackQuery->id, $context->currentStopDraft->countryName))
            ->add(new CityInputViewData($update->getChatId()))
            ->setNextState(States::WaitingForCityInput);

        return $viewDataCollection;
    }

    private function processCountryPicked(TelegramUpdate $update): void
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $countryPlaceId = $update->getCustomCallbackQueryData(CallbackQueryData::Country);

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
    }
}
