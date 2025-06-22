<?php

namespace App\Service\FlowViewer\Callback\MenuActions\SettingsActions;

use App\DTO\Internal\DefaultCurrencyPickedViewData;
use App\DTO\Internal\MenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Repository\UserRepository;
use App\Service\CurrencyResolverService;
use App\Service\FlowViewer\StateAwareViewDataBuilderInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;
use Doctrine\ORM\EntityManagerInterface;

readonly class DefaultCurrencyCountryPickedViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserRepository $userRepository,
        private CurrencyResolverService $currencyResolverService,
        private EntityManagerInterface $entityManager,
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::DefaultCurrencyCountryPick);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForDefaultCurrencyPicked];
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
        $viewDataCollection
            ->add(new DefaultCurrencyPickedViewData($update->callbackQuery->id, $currencyCode))
            ->add(new MenuViewData($update->getChatId()));

        $this->userStateStorage->resetState($update->getChatId());

        return $viewDataCollection;
    }
}
