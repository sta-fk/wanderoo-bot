<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CityPickedViewData;
use App\DTO\Internal\CustomDurationInputViewData;
use App\DTO\Internal\DurationViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CityPickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlaceServiceInterface $placeService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::City->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCityPicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $cityPlaceId = substr($update->callbackQuery->data, strlen(CallbackQueryData::City->value));

        $cityDetails = $this->placeService->getPlaceDetails($cityPlaceId);

        $context->currentStopDraft->cityName = $cityDetails->name;
        $context->currentStopDraft->cityPlaceId = $cityPlaceId;

        $this->userStateStorage->saveContext($chatId, $context);

        $processedViewData = new CityPickedViewData($update->callbackQuery->id, $cityDetails->name);

        [$nextViewData, $nextState] =
            $context->isAddingStopFlow
            ? [new CustomDurationInputViewData($chatId), States::WaitingForCustomDurationInput]
            : [new DurationViewData($chatId), States::WaitingForDurationPicked];

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add($processedViewData)
            ->add($nextViewData)
            ->setNextState($nextState)
            ;

        return $viewDataCollection;
    }
}
