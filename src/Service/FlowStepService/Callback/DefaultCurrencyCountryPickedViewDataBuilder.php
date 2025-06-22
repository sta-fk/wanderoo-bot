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
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::DefaultCurrencyCountryPick->value);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $countryPlaceId = substr($update->callbackQuery->data, strlen(CallbackQueryData::DefaultCurrencyCountryPick->value));
        $countryDetails = $this->placeService->getPlaceDetails($countryPlaceId);

        $currencyCode = $this->currencyResolverService->resolveCurrencyCode($countryDetails->countryCode);

        $user = $this->userRepository->findOrCreateFromTelegramUpdate($update);
        $user->setDefaultCurrency($currencyCode);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection->add(new DefaultCurrencyPickedViewData($update->callbackQuery->id, $currencyCode));
        $viewDataCollection->add(new MenuViewData($chatId));

        return $viewDataCollection;
    }
}
