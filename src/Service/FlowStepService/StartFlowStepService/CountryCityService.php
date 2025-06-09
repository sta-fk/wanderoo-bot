<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\Place\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CountryCityService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserStateStorage      $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Country->value)
        ;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCountryCity];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $countryPlaceId = substr($update->callbackQuery->data, strlen(CallbackQueryData::Country->value));

        $countryDetails = $this->placeService->getPlaceDetails($countryPlaceId);

        $context->currentStopDraft->countryName = $countryDetails->name;
        $context->currentStopDraft->countryCode = $countryDetails->countryCode;
        $context->currentStopDraft->countryPlaceId = $countryPlaceId;

        $this->userStateStorage->saveContext($chatId, $context);

        return new SendMessageContext(
            $chatId,
            "Введіть назву міста (або частину назви):",
            null,
            States::WaitingForCitySearch
        );
    }
}
