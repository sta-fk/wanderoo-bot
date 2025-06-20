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
use App\Enum\States;
use App\Service\FlowStepService\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;
use Doctrine\DBAL\Schema\View;

readonly class CustomDurationInputViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->message;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCustomDurationInput];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (!is_numeric($update->message->text) || $update->message->text < 0 || $update->message->text >= 30) {
            return ViewDataCollection::createWithSingleViewData(
                new CustomDurationInputViewData($chatId, false)
            );
        }

        $context->currentStopDraft->duration = (int)$update->message->text;

        $this->userStateStorage->saveContext($chatId, $context);

        $processedViewData = new DurationProcessedViewData($chatId, $context->currentStopDraft->duration);

        [$nextViewData, $nextState] =
            $context->isAddingStopFlow
                ? [
                    new ReuseOrNewTripStyleViewData($chatId, ($context->stops[count($context->stops) - 1])->getTripStyleLabel()),
                    States::WaitingForReuseOrNewTripStyle
                ]
                : [new StartDateViewData($chatId), States::WaitingForStartDate];

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add($processedViewData)
            ->add($nextViewData)
            ->setNextState($nextState)
        ;

        return $viewDataCollection;
    }
}
