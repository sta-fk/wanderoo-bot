<?php

namespace App\Service\ViewDataBuilder\Callback\DraftStopFinishedActions;

use App\DTO\Internal\TripStopGenerationFinishedViewData\AddStopViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;
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
        $context->enableAddingStopFlow();
        $this->userStateStorage->saveContext($chatId, $context);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new AddStopViewData(
                chatId: $chatId,
                lastOneCountryName: $context->getLastSavedStop()->countryName
            ),
            States::WaitingForStopCountry
        );
    }
}
