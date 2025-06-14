<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CityPickService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::City->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCityPick];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $cityPlaceId = substr($update->callbackQuery->data, strlen(CallbackQueryData::City->value));

        $cityDetails = $this->placeService->getPlaceDetails($cityPlaceId);

        $context->currentStopDraft->cityName = $cityDetails->name;
        $context->currentStopDraft->cityPlaceId = $cityPlaceId;

        $this->userStateStorage->saveContext($chatId, $context);

        return $this->getSendMessageContext($chatId, $context);
    }

    private function getSendMessageContext(int $chatId, PlanContext $context): SendMessageContext
    {
        if ($context->isAddingStopFlow) {
            $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCustomDuration);

            return new SendMessageContext(
                $chatId,
                $nextStateKeyboardProvider->getTextMessage(),
                $nextStateKeyboardProvider->buildKeyboard(),
                States::WaitingForCustomDuration
            );
        }

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForDuration);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage(),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForDuration,
        );
    }
}
