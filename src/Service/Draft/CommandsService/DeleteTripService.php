<?php

namespace App\Service\Draft\CommandsService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Repository\TripRepository;
use App\Service\FlowStepServiceInterface;
use App\Service\Draft\KeyboardResolver\KeyboardProviderResolver;
use App\Service\UserStateStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class DeleteTripService implements FlowStepServiceInterface
{
    public function __construct(
        private TripRepository $tripRepository,
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::DeleteTrip->value);
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $tripId = substr($update->callbackQuery->data, strlen(CallbackQueryData::DeleteTrip->value));
        $trip = $this->tripRepository->findOneBy(['id' => $tripId]);

        if ($trip) {
            $this->entityManager->remove($trip);
            $this->entityManager->flush();
        }

        return new SendMessageContext(
            $update->callbackQuery->message->chat->id,
            $this->translator->trans('trip.deleted.success')
        );
    }
}
