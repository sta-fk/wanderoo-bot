<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Entity\Trip;
use App\Enum\CallbackQueryData;
use App\Repository\TripRepository;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardProvider\Callback\ViewTripPlanKeyboardProvider;
use App\Service\TripPlanMapper;
use App\Service\TripPlanner\TripPlanFormatterInterface;

class ViewPlanCallbackService implements FinalStateAwareFlowStepServiceInterface
{
    private ?string $viewTripPlanId = null;

    public function __construct(
        private readonly TripRepository $tripRepository,
        private readonly ViewTripPlanKeyboardProvider $keyboardProvider,
        private readonly TripPlanFormatterInterface $formatter,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::ViewTrip->value);
    }

    public function getSplitFormattedPlan(TelegramUpdate $update): array
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $tripId = substr($update->callbackQuery->data, strlen(CallbackQueryData::ViewTrip->value));
        /** @var Trip $trip */
        $trip = $this->tripRepository->findOneBy(['id' => $tripId]);

        if (!$trip) {
            return [
                new SendMessageContext(
                    $chatId,
                    'План не знайдено'
                )
            ];
        }

        $tripPlan = TripPlanMapper::fromEntity($trip);
        $this->viewTripPlanId = $trip->getId();
        $texts = $this->formatter->splitFormattedPlan($tripPlan);

        $i = 0;
        $messages = [];
        while ($i < count($texts)) {
            $messages[] = new SendMessageContext(
                $chatId,
                $texts[$i],
            );
            $i++;
        }


        // Final message with keyboard
        $messages[] = $this->buildNextStepMessage($update);

        return $messages;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        if (!$this->viewTripPlanId) {
            throw new \LogicException("Keyboard is not configured");
        }

        return new SendMessageContext(
            $update->callbackQuery->message->chat->id,
            $this->keyboardProvider->getTextMessage(),
            $this->keyboardProvider
                ->setViewTripPlanId($this->viewTripPlanId)
                ->buildKeyboard(),
        );
    }
}
