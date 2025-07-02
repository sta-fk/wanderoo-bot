<?php

namespace App\Service\ViewDataBuilder\Commands;

use App\DTO\Internal\ViewDataCollection;
use App\DTO\Internal\MenuActionsViewData\ViewSavedPlansListViewData;
use App\DTO\Request\TelegramUpdate;
use App\Enum\TelegramCommands;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;

readonly class ViewSavedPlansListViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private TripRepository $tripRepository,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsMessageUpdate(TelegramCommands::ViewSavedPlansList);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $user = $this->userRepository->findOneBy(['chatId' => $chatId]);
        $trips = $this->tripRepository->findBy(['user' => $user]);

        return ViewDataCollection::createWithSingleViewData(new ViewSavedPlansListViewData($chatId, $trips));
    }
}
