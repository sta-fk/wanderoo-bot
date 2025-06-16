<?php

namespace App\Service\KeyboardProvider\Callback;

use App\DTO\Request\TelegramCallbackQuery;
use App\Entity\Trip;
use App\Enum\CallbackQueryData;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ViewAllPlansCallbackKeyboardProvider implements CallbackKeyboardProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private TripRepository $tripRepository,
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(TelegramCallbackQuery $callbackQuery): bool
    {
        return CallbackQueryData::ViewAllTrips->value === $callbackQuery->data;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        return $this->translator->trans('trip.list.select');
    }

    public function buildKeyboard(int $chatId = 0): ?array
    {
        $user = $this->userRepository->findOneBy(['chatId' => $chatId]);
        $trips = $this->tripRepository->findBy(['user' => $user]);

        $inlineKeyboard = array_map(static function (Trip $trip) {
            return [[
                'text' => sprintf("➡️ %s", $trip->getTitle()),
                'callback_data' => CallbackQueryData::ViewTrip->value . $trip->getId()
            ]];
        }, $trips);

        return [
            'inline_keyboard' => $inlineKeyboard
        ];
    }
}
