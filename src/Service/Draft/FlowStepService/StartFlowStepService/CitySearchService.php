<?php

namespace App\Service\Draft\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\Draft\FlowStepService\StateAwareFlowViewDataServiceInterface;
use App\Service\Draft\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CitySearchService implements StateAwareFlowViewDataServiceInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->message
            && null !== $update->message->text;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCitySearch];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $countryCode = $context->currentStopDraft?->countryCode ?? null;

        $cities = $this->placeService->searchCities($update->message->text, $countryCode);

        if (empty($cities)) {
            return new SendMessageContext($chatId, "Не знайдено такого міста. Спробуйте ще раз.");
        }

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCityPick);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage(),
            $nextStateKeyboardProvider->buildKeyboard($cities),
            States::WaitingForCityPick
        );
    }
}
