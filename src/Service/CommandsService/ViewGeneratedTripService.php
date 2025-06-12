<?php

namespace App\Service\CommandsService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\TripPlanner\PlanBuilderService;
use App\Service\TripPlanner\TripPlanFormatterInterface;
use App\Service\UserStateStorage;

readonly class ViewGeneratedTripService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlanBuilderService $planBuilderService,
        private TripPlanFormatterInterface $tripPlanFormatter,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text === TelegramCommands::ViewGeneratedPlan->value;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

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
