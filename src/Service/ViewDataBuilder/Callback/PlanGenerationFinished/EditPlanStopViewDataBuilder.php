<?php

namespace App\Service\ViewDataBuilder\Callback\PlanGenerationFinished;

use App\DTO\Internal\PlanGenerationFinishedViewData\EditPlanStopViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;

readonly class EditPlanStopViewDataBuilder implements StateAwareViewDataBuilderInterface
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
        return [States::EditingPlanStop];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $stopIndex = (int) $update->getCustomCallbackQueryData(CallbackQueryData::EditPlanStop);
        $context = $this->userStateStorage->getContext($chatId);
        if (!isset($context->stops[$stopIndex])) {
            throw new \RuntimeException('Trip stop not found in PlanContext');
        }

        $context->editingStopIndex = $stopIndex;
        $this->userStateStorage->saveContext($chatId, $context);

        return ViewDataCollection::createWithSingleViewData(
            new EditPlanStopViewData(
                $chatId,
                $context->stops[$stopIndex]->countryName,
                $context->stops[$stopIndex]->cityName,
                $stopIndex
            ),
        );
    }
}
