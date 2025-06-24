<?php

namespace App\Service\ViewDataBuilder\Callback\PlanGenerationFinished;

use App\DTO\Internal\PlanGenerationFinishedViewData\EditTripStopViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;

readonly class EditTripStopViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::EditPlanStop);
    }

    public function supportsStates(): array
    {
        return [States::EditingTripStop];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $callbackData = $update->callbackQuery?->data;

        $stopIndex = (int) CallbackQueryData::EditPlanStop->parseQuery($callbackData);
        $context = $this->userStateStorage->getContext($chatId);
        if (!isset($context->stops[$stopIndex])) {
            throw new \RuntimeException('Trip stop not found in PlanContext');
        }

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new EditTripStopViewData(
                $chatId,
                $context->stops[$stopIndex]->countryName,
                $context->stops[$stopIndex]->cityName,
                $stopIndex
            ),
            States::EditingTripStop,
        );
    }
}
