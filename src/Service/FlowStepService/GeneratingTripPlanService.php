<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepServiceInterface;
use App\Service\TripPlanner\PlanBuilderService;
use App\Service\TripPlanner\TripPlanFormatterInterface;
use App\Service\UserStateStorage;

readonly class GeneratingTripPlanService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlanBuilderService $planBuilderService,
        private TripPlanFormatterInterface $tripPlanFormatter,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::GeneratingTripPlan->value === $update->callbackQuery->data
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $context->finishCreatingNewStop();
        $context->disableAddingStopFlow();

        $this->userStateStorage->saveContext($chatId, $context);

        $tripPlan = $this->planBuilderService->buildPlan($context);
        $message = $this->tripPlanFormatter->format($tripPlan);

        return new SendMessageContext(
            $chatId,
            $message . "\n\n" . "Що бажаєте зробити з цим маршрутом?",
            $this->getKeyboard(),
        );
    }

    private function getKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Зберегти план', 'callback_data' => 'save_generated_plan'],
                ],
                [
                    ['text' => '✏️ Змінити план', 'callback_data' => 'edit_generated_plan'],
                ],
                [
                    ['text' => '🔄 Почати заново', 'callback_data' => CallbackQueryData::NewTrip->value],
                ],
                [
                    ['text' => '🔙 Назад', 'callback_data' => 'back_to_main_menu'],
                ],
            ]
        ];
    }
}
