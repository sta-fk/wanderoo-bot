<?php

namespace App\Service\Draft\KeyboardProvider\Message;

use App\DTO\Request\TelegramMessage;
use App\Entity\Trip;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Service\Draft\KeyboardProvider\Message\MessageKeyboardProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ViewAllPlansKeyboardProvider implements MessageKeyboardProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private TripRepository $tripRepository,
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(TelegramMessage $message): bool
    {
        return TelegramCommands::ViewAllPlans->value === $message->text;
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
