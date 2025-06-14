<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\CurrencyResolverService;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class StopCountryService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $nextStateKeyboardProviderResolver,
        private CurrencyResolverService $currencyResolverService,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && (CallbackQueryData::StopCountryAnother->value === $update->callbackQuery->data
            || CallbackQueryData::StopCountrySame->value === $update->callbackQuery->data)
        ;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStopCountry];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        if (CallbackQueryData::StopCountryAnother->value === $update->callbackQuery->data) {
            return $this->buildContextWithAnotherCountry($update);
        }

        return $this->buildContextWithSameCountry($update);
    }

    private function buildContextWithAnotherCountry(TelegramUpdate $update): SendMessageContext
    {
        $nextStateKeyboardProvider = $this->nextStateKeyboardProviderResolver->resolve(States::WaitingForCountry);

        return new SendMessageContext(
            $update->callbackQuery->message->chat->id,
            $nextStateKeyboardProvider->getTextMessage(),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForCountry
        );
    }

    private function buildContextWithSameCountry(TelegramUpdate $update): SendMessageContext
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

        $nextStateKeyboardProvider = $this->nextStateKeyboardProviderResolver->resolve(States::WaitingForCitySearch);

        return new SendMessageContext(
            $update->callbackQuery->message->chat->id,
            $nextStateKeyboardProvider->getTextMessage(),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForCitySearch
        );
    }
}
