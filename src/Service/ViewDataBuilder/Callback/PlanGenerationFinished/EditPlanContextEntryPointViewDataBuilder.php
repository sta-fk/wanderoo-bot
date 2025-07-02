<?php

namespace App\Service\ViewDataBuilder\Callback\PlanGenerationFinished;

use App\DTO\Internal\PlanGenerationFinishedViewData\EditPlanContextEntryPointViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class EditPlanContextEntryPointViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::EditGeneratedPlan);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        return ViewDataCollection::createStateAwareWithSingleViewData(
            new EditPlanContextEntryPointViewData(
                $update->getChatId(),
                $this->userStateStorage->getContext($update->getChatId())->stops,
            ),
            States::EditingPlanStop
        );
    }
}
