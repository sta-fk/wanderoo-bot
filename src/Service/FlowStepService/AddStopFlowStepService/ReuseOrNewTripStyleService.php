<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\GetTripStyleKeyboardTrait;
use App\Service\UserStateStorage;

class ReuseOrNewTripStyleService implements StateAwareFlowStepServiceInterface
{
    use GetTripStyleKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::TripStyle->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForReuseOrNewTripStyle];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $action = substr($update->callbackQuery->data, strlen(CallbackQueryData::TripStyle->value));

        if ($action === CallbackQueryData::Reuse->value) {
            $lastOneStop = ($context->stops[count($context->stops) - 1]);
            $currentStopDraft = $context->currentStopDraft;

            $currentStopDraft->tripStyle = $lastOneStop->tripStyle;
            $this->userStateStorage->saveContext($chatId, $context);

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '✅ Так', 'callback_data' => CallbackQueryData::Interest->value . CallbackQueryData::Reuse->value],
                        ['text' => '❌ Ні', 'callback_data' => CallbackQueryData::Interest->value . CallbackQueryData::New->value]
                    ],
                ]
            ];

            return new SendMessageContext(
                $chatId,
                "Стиль подорожі для {$currentStopDraft->cityName}: <b>{$currentStopDraft->tripStyle}</b>.\n\nНаступний крок...\n\n✨ Використати попередні інтереси для цієї зупинки?",
                $keyboard,
                States::WaitingForReuseOrNewInterests
            );
        }

        $keyboard = $this->getTripStyleKeyboard();
        $text = "Який стиль подорожі ви бажаєте? 🧳";

        return new SendMessageContext($chatId, $text, $keyboard, States::WaitingForTripStyle);

    }
}
