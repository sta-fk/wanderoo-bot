<?php

namespace App\Service\Draft\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\Draft\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\Draft\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\Integrations\PlaceServiceInterface;

readonly class CountrySearchService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->message
            && null !== $update->message->text;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCountry];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $countries = $this->placeService->searchCountries($update->message->text);

        if (empty($countries)) {
            return new SendMessageContext($chatId, "Не знайдено такої країни. Спробуйте ще раз.");
        }

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCountryPick);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage(),
            $nextStateKeyboardProvider->buildKeyboard($countries),
            States::WaitingForCountryPick
        );
    }
}
