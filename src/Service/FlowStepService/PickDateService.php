<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\UserStateStorage;

class PickDateService implements StateAwareFlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, CallbackQueryData::PickDate->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForStartDate];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $dateStr = substr($update->callbackQuery->data, strlen(CallbackQueryData::PickDate->value)); // YYYY-MM-DD
        $chatId = $update->callbackQuery->message->chat->id;

        $context = $this->userStateStorage->getContext($chatId);
        if (null === $context->duration) {
            throw new \RuntimeException("Invalid payload");
        }

        $context->startDate = $dateStr;

        $endDate = (new \DateTimeImmutable($dateStr))->modify("+$context->duration days");
        $context->endDate = $endDate->format('Y-m-d');

        $this->userStateStorage->saveContext($chatId, $context);

        $keyboard = [
            [
                ['text' => '🧘 Лайтовий', 'callback_data' => CallbackQueryData::TripStyle->value . 'лайтовий'],
                ['text' => '🚀 Активний', 'callback_data' => CallbackQueryData::TripStyle->value . 'активний'],
                ['text' => '🎭 Змішаний', 'callback_data' => CallbackQueryData::TripStyle->value . 'змішаний'],
            ],
        ];

        $text = "✅ Подорож з <b>$dateStr</b> по <b>{$endDate->format('Y-m-d')}</b> \n\nЯкий стиль подорожі ви бажаєте? 🧳";

        return new SendMessageContext($chatId, $text, ['inline_keyboard' => $keyboard], States::WaitingForTripStyle);
    }
}
