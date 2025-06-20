<?php

namespace App\Service\FlowStepService\Commands;

use App\DTO\Internal\ViewDataCollection;
use App\DTO\Internal\ViewSavedPlansListViewData;
use App\DTO\Request\TelegramUpdate;
use App\Enum\TelegramCommands;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Service\FlowStepService\ViewDataBuilderInterface;

readonly class ViewSavedPlansListViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private TripRepository $tripRepository,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return TelegramCommands::ViewSavedPlansList->value === $update->message?->text;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->message->chat->id;
        $user = $this->userRepository->findOneBy(['chatId' => $chatId]);
        $trips = $this->tripRepository->findBy(['user' => $user]);

        return ViewDataCollection::createWithSingleViewData(new ViewSavedPlansListViewData($chatId, $trips));
    }
}
