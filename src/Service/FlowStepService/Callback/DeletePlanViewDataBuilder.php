<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\DeletePlanViewData;
use App\DTO\Internal\UniversalDeletePreviousMessageViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Internal\ViewSavedPlansListViewData;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Repository\TripRepository;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class DeletePlanViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private TripRepository $tripRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::DeletePlan->value);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $tripId = substr($update->callbackQuery->data, strlen(CallbackQueryData::DeletePlan->value));
        $trip = $this->tripRepository->findOneBy(['id' => $tripId]);

        if ($trip) {
            $this->entityManager->remove($trip);
            $this->entityManager->flush();
        }

        $viewDataCollection = new ViewDataCollection();
//        $viewDataCollection->add(new UniversalDeletePreviousMessageViewData($update->callbackQuery->message->chat->id, $update->callbackQuery->message->messageId));
        $viewDataCollection->add(new DeletePlanViewData($update->callbackQuery->id));
        $viewDataCollection->add(new ViewSavedPlansListViewData($update->callbackQuery->message->chat->id));

        return $viewDataCollection;
    }
}
