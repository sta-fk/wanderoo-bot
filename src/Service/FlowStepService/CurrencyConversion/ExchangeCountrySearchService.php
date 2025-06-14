<?php

namespace App\Service\FlowStepService\CurrencyConversion;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\KeyboardResolver\NextStateKeyboardProviderResolver;

readonly class ExchangeCountrySearchService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
        private PlaceServiceInterface $placeService,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->message
            && null !== $update->message->text;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForExchangeCountrySearch];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $countries = $this->placeService->searchCountries($update->message->text);

        if (empty($countries)) {
            return new SendMessageContext($chatId, "Не знайдено такої країни. Спробуйте ще раз.");
        }

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForExchangeCountryPick);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage(),
            $nextStateKeyboardProvider->buildKeyboard($countries),
            States::WaitingForExchangeCountryPick,
        );
    }
}
