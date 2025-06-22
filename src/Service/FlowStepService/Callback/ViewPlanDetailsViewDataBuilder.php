<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\PlanIsGeneratingViewData;
use App\DTO\Internal\SavedPlanNotFoundViewData;
use App\DTO\Internal\PlanDetailsShownViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Entity\Trip;
use App\Enum\CallbackQueryData;
use App\Repository\TripRepository;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\TripPlanMapper;
use App\Service\TripPlanner\TripPlanFormatterInterface;

readonly class ViewPlanDetailsViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private TripRepository $tripRepository,
        private TripPlanFormatterInterface $formatter,
        private TripPlanMapper $tripPlanMapper,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::ViewPlanDetails);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $tripId = $update->supportsCallbackQuery(CallbackQueryData::ViewPlanDetails);
        /** @var Trip $trip */
        $trip = $this->tripRepository->findOneBy(['id' => $tripId]);

        if (!$trip) {
            return ViewDataCollection::createWithSingleViewData(
                new SavedPlanNotFoundViewData($update->callbackQuery->id)
            );
        }

        $tripPlan = $this->tripPlanMapper->fromEntity($trip);
        $texts = $this->formatter->splitFormattedPlan($tripPlan);
        $viewDataCollection = new ViewDataCollection();

        $i = 0;
        while ($i < count($texts)) {
            $viewDataCollection->add(new PlanIsGeneratingViewData($chatId, $texts[$i]));
            $i++;
        }

        $viewDataCollection->add(new PlanDetailsShownViewData($chatId, $trip->getId()));

        return $viewDataCollection;
    }
}
