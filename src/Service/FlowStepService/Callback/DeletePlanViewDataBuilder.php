<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\DeletePlanViewData;
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
        return $update->supportsCallbackQuery(CallbackQueryData::DeletePlan);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $trip = $this->tripRepository->findOneBy([
            'id' => $update->getCustomCallbackQueryData(CallbackQueryData::DeletePlan)
        ]);

        if ($trip) {
            $this->entityManager->remove($trip);
            $this->entityManager->flush();
        }

        $viewDataCollection = new ViewDataCollection();
//        $viewDataCollection->add(new UniversalDeletePreviousMessageViewData($update->callbackQuery->message->chat->id, $update->callbackQuery->message->messageId));
        $viewDataCollection->add(new DeletePlanViewData($update->callbackQuery->id));
        $viewDataCollection->add(new ViewSavedPlansListViewData($update->getChatId()));

        return $viewDataCollection;
    }
}
