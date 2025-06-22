<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\DefaultCurrencyPickedViewData;
use App\DTO\Internal\MenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Repository\UserRepository;
use App\Service\CurrencyResolverService;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class DefaultCurrencyCountryPickedViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserRepository $userRepository,
        private CurrencyResolverService $currencyResolverService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::DefaultCurrencyCountryPick);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $countryDetails = $this->placeService->getPlaceDetails(
            $update->getCustomCallbackQueryData(CallbackQueryData::DefaultCurrencyCountryPick)
        );

        $currencyCode = $this->currencyResolverService->resolveCurrencyCode($countryDetails->countryCode);

        $user = $this->userRepository->findOrCreateFromTelegramUpdate($update);
        $user->setDefaultCurrency($currencyCode);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection->add(new DefaultCurrencyPickedViewData($update->callbackQuery->id, $currencyCode));
        $viewDataCollection->add(new MenuViewData($update->getChatId()));

        return $viewDataCollection;
    }
}
