<?php

namespace App\Service\ViewDataBuilder\Callback\MenuActions\ViewPlanDetailsActions;

use App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData\ViewedPlanCurrencyChangedViewData;
use App\DTO\Internal\MenuActionsViewData\ViewSavedPlansListViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Repository\TripRepository;
use App\Service\ViewDataBuilder\ViewDataBuilderInterface;
use App\Service\UserStateStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class SetViewedPlanCurrencyViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private TripRepository $tripRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::SetViewedPlanCurrency);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $callbackQueryData = $update->getCustomCallbackQueryData(CallbackQueryData::SetViewedPlanCurrency);
        $values = explode('_', $callbackQueryData);
        [ $tripId, $currencyValue] = [ $values[0], $values[1] ];

        if (!preg_match('/^[a-f0-9]{8}$/', $tripId)) {
            throw new \InvalidArgumentException('Invalid short ID format');
        }

        $trip = $this->tripRepository->findByShortUuid($tripId);
        if (null === $trip) {
            throw new NotFoundHttpException();
        }

//        if (CallbackQueryData::Auto->value === $currencyValue) {
//            return ViewDataCollection::createStateAwareWithSingleViewData(
//                new ViewedPlanCurrencyCountryInputViewData($update->getChatId()),
//                States::WaitingForViewedPlanCurrencyCountryInput
//            );
//        }

        //TODO: Would be better add checking for target as related currencies
        $trip->setCurrency($currencyValue);
        $this->entityManager->persist($trip);
        $this->entityManager->flush();

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add(new ViewedPlanCurrencyChangedViewData($update->callbackQuery->id, $currencyValue))
            ->add(new ViewSavedPlansListViewData($update->getChatId(), $this->tripRepository->findBy(['user' => $trip->getUser()])));

        return $viewDataCollection;
    }
}
