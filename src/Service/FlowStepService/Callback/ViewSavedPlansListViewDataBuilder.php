<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\ViewDataCollection;
use App\DTO\Internal\ViewSavedPlansListViewData;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
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
        return null !== $update->callbackQuery
            && CallbackQueryData::ViewSavedPlansList->value === $update->callbackQuery->data;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $user = $this->userRepository->findOneBy(['chatId' => $chatId]);
        $trips = $this->tripRepository->findBy(['user' => $user]);

        return ViewDataCollection::createWithSingleViewData(new ViewSavedPlansListViewData($chatId, $trips));
    }
}
