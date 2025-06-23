<?php

namespace App\Service\FlowViewer\Callback\MenuActions\ViewPlanDetailsActions;

use App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData\ViewedPlanExchangerViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Entity\Trip;
use App\Entity\TripStop;
use App\Entity\User;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Service\FlowViewer\ViewDataBuilderInterface;
use App\Service\Integrations\CurrencyExchangerService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

readonly class ViewedPlanExchangerViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private TripRepository $tripRepository,
        private UserRepository $userRepository,
        private CurrencyExchangerService $currencyService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::ViewedPlanExchanger);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $user = $this->userRepository->findOneBy(['chatId' => $update->getChatId()]);
        $trip = $this->tripRepository->findOneBy([
            'id' => $update->getCustomCallbackQueryData(CallbackQueryData::ViewedPlanExchanger)
        ]);
        if (!$user instanceof User || !$trip instanceof Trip) {
            throw new NotFoundHttpException('Trip not found');
        }

        $baseCurrency = $trip->getCurrency();
        $baseAmount = $this->getTotalBudget($trip);

        $relatedCurrencies = array_filter(array_unique(array_map(
            static fn(TripStop $stop) => $stop->getCurrency(),
            $trip->getStops()->toArray()
        )));

        $targetCurrencies = array_unique(array_merge(
            [CallbackQueryData::Usd->value, CallbackQueryData::Eur->value],
            [$user->getDefaultCurrency()],
            $relatedCurrencies
        ));

        $converted = $this->currencyService->convertToMultiple($baseCurrency, $targetCurrencies, $baseAmount);

        return ViewDataCollection::createWithSingleViewData(
            new ViewedPlanExchangerViewData(
                chatId: $update->getChatId(),
                tripId: Uuid::fromString($trip->getId()),
                baseAmount: $baseAmount,
                baseCurrency: $baseCurrency,
                convertedAmounts: $converted,
                userDefaultCurrency: $user->getDefaultCurrency(),
            )
        );
    }

    private function getTotalBudget(Trip $trip): float
    {
        $totalBudget = 0.0;
        $targetCurrency = $trip->getCurrency();
        $stops = $trip->getStops()->toArray();

        foreach ($stops as $stop) {
            $totalBudget += $this->currencyService->convert($stop->getBudget(), $stop->getCurrency(), $targetCurrency);
        }

        return round($totalBudget, -1);
    }
}
