<?php

namespace App\Service\CommandsService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Entity\Trip;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Service\FlowStepService\FinalStateAwareFlowStepServiceInterface;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardResolver\KeyboardProviderResolver;
use App\Service\TripPlanner\PlanBuilderService;
use App\Service\TripPlanner\TripPlanFormatterInterface;
use App\Service\UserStateStorage;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ViewAllPlansService implements FlowStepServiceInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private TripRepository $tripRepository,
        private TranslatorInterface $translator,
        private KeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return TelegramCommands::ViewAllPlans->value === $update->message?->text;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $user = $this->userRepository->findOrCreateFromTelegramUpdate($update);
        $trips = $this->tripRepository->findBy(['user' => $user]);

        if (empty($trips)) {
            return new SendMessageContext($chatId, $this->translator->trans('trip.list.empty'));
        }

        $keyboardProvider = $this->keyboardProviderResolver->resolve($update);

        return new SendMessageContext(
            $chatId,
            $keyboardProvider->getTextMessage(),
            $keyboardProvider->buildKeyboard($chatId)
        );
    }
}
