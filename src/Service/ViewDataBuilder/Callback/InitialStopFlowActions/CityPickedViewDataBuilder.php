<?php

namespace App\Service\ViewDataBuilder\Callback\InitialStopFlowActions;

use App\DTO\Internal\InitialStopFlowViewData\CityPickedViewData;
use App\DTO\Internal\AddStopFlowViewData\CustomDurationInputViewData;
use App\DTO\Internal\InitialStopFlowViewData\DurationViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class CityPickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlaceServiceInterface $placeService,
        private TranslatorInterface $translator,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::City);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCityPicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $this->processCityPicked($update);

        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        [$nextViewData, $nextState] = [new DurationViewData($chatId), States::WaitingForDurationPicked];

        if ($context->isAddingStopFlow) {
            [$nextViewData, $nextState] = [
                new CustomDurationInputViewData($chatId),
                States::WaitingForCustomDurationInput
            ];
        }

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add(new CityPickedViewData($update->callbackQuery->id, $context->currentStopDraft->cityName))
            ->add($nextViewData)
            ->setNextState($nextState);

        return $viewDataCollection;
    }

    private function processCityPicked(TelegramUpdate $update): void
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $cityPlaceId = $update->getCustomCallbackQueryData(CallbackQueryData::City);
        $cityDetails = $this->placeService->getPlaceDetails($cityPlaceId);

        $context->currentStopDraft->cityName = $cityDetails->name;
        $context->currentStopDraft->cityPlaceId = $cityPlaceId;

        $this->userStateStorage->saveContext($chatId, $context);
    }
}
