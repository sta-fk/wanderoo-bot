<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\CityInputSearchResultViewData;
use App\DTO\Internal\CountryInputSearchResultViewData;
use App\DTO\Internal\CustomDurationInputViewData;
use App\DTO\Internal\DurationProcessedViewData;
use App\DTO\Internal\CustomDurationValidationFailedViewData;
use App\DTO\Internal\ReuseOrNewTripStyleViewData;
use App\DTO\Internal\StartDateViewData;
use App\DTO\Internal\StartNewViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;
use Doctrine\DBAL\Schema\View;

readonly class DurationViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Duration->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForDurationPicked];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $durationValue = substr($update->callbackQuery->data, strlen(CallbackQueryData::Duration->value));
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (CallbackQueryData::Custom->value === $durationValue) {
            return ViewDataCollection::createStateAwareWithSingleViewData(
                new CustomDurationInputViewData($chatId),
                States::WaitingForCustomDurationInput,
            );
        }

        $context->currentStopDraft->duration = (int) $durationValue;
        $this->userStateStorage->saveContext($chatId, $context);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection->add(new DurationProcessedViewData($chatId, $context->currentStopDraft->duration));
        $viewDataCollection->add(new StartDateViewData($chatId));
        $viewDataCollection->setNextState(States::WaitingForStartDate);

        return $viewDataCollection;
    }
}
