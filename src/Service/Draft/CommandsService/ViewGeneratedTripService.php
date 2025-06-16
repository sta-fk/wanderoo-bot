<?php

namespace App\Service\Draft\CommandsService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Service\Draft\FlowStepService\FinalStateAwareFlowStepServiceInterface;
use App\Service\TripPlanner\PlanBuilderService;
use App\Service\TripPlanner\TripPlanFormatterInterface;
use App\Service\UserStateStorage;

readonly class ViewGeneratedTripService implements FinalStateAwareFlowStepServiceInterface
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

    public function getSplitFormattedPlan(TelegramUpdate $update): array
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $context->finishCreatingNewStop();
        $context->disableAddingStopFlow();

        $this->userStateStorage->saveContext($chatId, $context);

        $tripPlan = $this->planBuilderService->buildPlan($context);

        $texts = $this->tripPlanFormatter->splitFormattedPlan($tripPlan);

        $i = 0;
        $messages = [];
        while ($i < count($texts)) {
            $messages[] = new SendMessageContext(
                $chatId,
                $texts[$i],
            );
            $i++;
        }

        $messages[] = $this->buildNextStepMessage($update);

        return $messages;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        return new SendMessageContext(
            $update->message->chat->id,
            "Що бажаєте зробити з цим маршрутом?",
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
