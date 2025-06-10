<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\CurrencyResolverService;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\NextStateKeyboardProviderResolver;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CurrencyCountryPickService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
        private CurrencyResolverService $currencyResolverService,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::CurrencyCountryPick->value)
        ;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCurrencyCountryPick];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $countryPlaceId = substr($update->callbackQuery->data, strlen(CallbackQueryData::CurrencyCountryPick->value));
        $countryDetails = $this->placeService->getPlaceDetails($countryPlaceId);

        $currency = $this->currencyResolverService->resolveCurrencyCode($countryDetails->countryCode);

        $context->currency = $currency;
        $this->userStateStorage->saveContext($chatId, $context);

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCustomBudget);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage(),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForCustomBudget
        );
    }
}
