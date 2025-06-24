<?php

namespace App\Service\ViewDataBuilder\Commands;

use App\DTO\Context\PlanContext;
use App\DTO\Internal\InitialStopFlowViewData\CountryInputViewData;
use App\DTO\Internal\MenuActionsViewData\StartNewViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class StartNewViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsMessageUpdate(TelegramCommands::StartNew);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();

        $this->userStateStorage->clearContext($chatId);
        $this->userStateStorage->saveContext($chatId, new PlanContext());

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection->add(new StartNewViewData($chatId));
        $viewDataCollection->add(new CountryInputViewData($chatId));
        $viewDataCollection->setNextState(States::WaitingForCountryInput);

        return $viewDataCollection;
    }
}
