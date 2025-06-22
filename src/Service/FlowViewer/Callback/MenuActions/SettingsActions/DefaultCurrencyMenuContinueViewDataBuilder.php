<?php

namespace App\Service\FlowViewer\Callback\MenuActions\SettingsActions;

use App\DTO\Internal\DefaultCurrencyCountryInputViewData;
use App\DTO\Internal\DefaultCurrencyPickedViewData;
use App\DTO\Internal\MenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Repository\UserRepository;
use App\Service\FlowViewer\StateAwareViewDataBuilderInterface;
use App\Service\UserStateStorage;
use Doctrine\ORM\EntityManagerInterface;

readonly class DefaultCurrencyMenuContinueViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->supportsCallbackQuery(CallbackQueryData::DefaultCurrencyChoice);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForDefaultCurrencyMenuContinue];
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $choice = $update->getCustomCallbackQueryData(CallbackQueryData::DefaultCurrencyChoice);

        if ($choice === CallbackQueryData::Auto->value) {
            return ViewDataCollection::createStateAwareWithSingleViewData(
                new DefaultCurrencyCountryInputViewData($chatId),
                States::WaitingForDefaultCurrencyCountryInput
            );
        }

        if ($choice !== CallbackQueryData::Usd->value && $choice !== CallbackQueryData::Eur->value) {
            throw new \RuntimeException('Invalid currency choice');
        }

        $user = $this->userRepository->findOrCreateFromTelegramUpdate($update);
        $user->setDefaultCurrency($choice);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->userStateStorage->resetState($chatId);

        $viewDataCollection = new ViewDataCollection();
        $viewDataCollection
            ->add(new DefaultCurrencyPickedViewData($update->callbackQuery->id, $choice))
            ->add(new MenuViewData($chatId));

        return $viewDataCollection;
    }
}
