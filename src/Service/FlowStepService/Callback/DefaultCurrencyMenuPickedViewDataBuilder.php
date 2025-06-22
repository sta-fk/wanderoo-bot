<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\DefaultCurrencyCountryInputViewData;
use App\DTO\Internal\DefaultCurrencyPickedViewData;
use App\DTO\Internal\MenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Repository\UserRepository;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class DefaultCurrencyMenuPickedViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::DefaultCurrencyChoice);
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $choice = $update->getCustomCallbackQueryData(CallbackQueryData::DefaultCurrencyChoice);

        if ($choice === CallbackQueryData::Auto->value) {
            return ViewDataCollection::createWithSingleViewData(new DefaultCurrencyCountryInputViewData($chatId));
        }

        if ($choice !== CallbackQueryData::Usd->value && $choice !== CallbackQueryData::Eur->value) {
            throw new \RuntimeException('Invalid currency choice');
        }

        $user = $this->userRepository->findOrCreateFromTelegramUpdate($update);
        $user->setDefaultCurrency($choice);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection->add(new DefaultCurrencyPickedViewData($update->callbackQuery->id, $choice));
        $viewDataCollection->add(new MenuViewData($chatId));

        return $viewDataCollection;
    }
}
