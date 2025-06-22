<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\AddStopViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class AddStopViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::AddStop);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->userStateStorage->getContext($chatId);

        $viewDataCollection = ViewDataCollection::createStateAwareWithSingleViewData(
            new AddStopViewData(
                $chatId,
                $context->getLastSavedStop()->countryName
            ),
            States::WaitingForStopCountry
        );

        $context->enableAddingStopFlow();

        $this->userStateStorage->saveContext($chatId, $context);

        return $viewDataCollection;
    }
}
