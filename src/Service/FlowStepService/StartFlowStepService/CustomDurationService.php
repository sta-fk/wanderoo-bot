<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\KeyboardService\BuildCalendarKeyboardTrait;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\BuildGeneralKeyboardTrait;
use App\Service\UserStateStorage;

class CustomDurationService implements StateAwareFlowStepServiceInterface
{
    use BuildCalendarKeyboardTrait;
    use BuildGeneralKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->message
            && States::WaitingForCustomDuration === $this->userStateStorage->getState($update->message->chat->id);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCustomDuration];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (!is_numeric($update->message->text) || $update->message->text < 0 || $update->message->text >= 30) {
            return new SendMessageContext($chatId, "Будь ласка, введіть число від 1 до 30.", null, States::WaitingForCustomDuration);
        }

        $context->currentStopDraft->duration = (int)$update->message->text;
        $this->userStateStorage->saveContext($chatId, $context);

        return $this->getSendMessageContext($chatId, $context);
    }

    private function getSendMessageContext(int $chatId, PlanContext $context): SendMessageContext
    {
        if ($context->isAddingStopFlow) {
            $lastOneTripStyle = ($context->stops[count($context->stops) - 1])->tripStyle;
            $text = "Стиль минулої подорожі $lastOneTripStyle. Бажаєте зберегти для цієї?";
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['✅ Так', CallbackQueryData::TripStyle->value . CallbackQueryData::Reuse->value],
                        ['❌ Ні', CallbackQueryData::TripStyle->value . CallbackQueryData::New->value],
                    ]
                ]
            ];

            return new SendMessageContext(
                $chatId,
                $text,
                $keyboard,
                States::WaitingForReuseOrNewTripStyle
            );
        }

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $keyboard = $this->buildCalendarKeyboard($now->format('Y'), $now->format('m'));
        $text = "Чудово! Подорож на {$context->currentStopDraft->duration} днів. \n\n📅 Тепер оберіть дату подорожі:";

        return new SendMessageContext($chatId, $text, $keyboard, States::WaitingForStartDate);
    }
}
