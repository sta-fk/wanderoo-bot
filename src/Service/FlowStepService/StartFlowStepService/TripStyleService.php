<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardResolver\NextStateKeyboardProviderResolver;
use App\Service\UserStateStorage;

readonly class TripStyleService implements StateAwareFlowStepServiceInterface
{
    public const TRIP_STYLE_OPTIONS = [
        'light' => '🧘 Лайтовий',
        'active' => '🚀 Активний',
        'mixed' => '🎭 Змішаний',
        'relax' => '🛌 Релакс',
        'cultural' => '🏛️ Культурний',
        'roadtrip' => '🚗 Роадтрип',
        'luxury' => '💎 Люкс',
        'budget' => '💰 Бюджетний',
    ];

    public function __construct(
        private UserStateStorage $userStateStorage,
        private NextStateKeyboardProviderResolver $keyboardProviderResolver,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::TripStyle->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForTripStyle];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $tripStyle = substr($update->callbackQuery->data, strlen(CallbackQueryData::TripStyle->value));
        $context->currentStopDraft->tripStyle = $tripStyle;

        $this->userStateStorage->saveContext($chatId, $context);

        return $this->getSendMessageContext($chatId, $context);
    }

    private function getSendMessageContext(int $chatId, PlanContext $context): SendMessageContext
    {
        if ($context->isAddingStopFlow) {
            $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForReuseOrNewInterests);

            return new SendMessageContext(
                $chatId,
                $nextStateKeyboardProvider->getTextMessage($chatId),
                $nextStateKeyboardProvider->buildKeyboard(),
                States::WaitingForReuseOrNewInterests
            );
        }

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForInterests);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage($chatId),
            $nextStateKeyboardProvider->buildKeyboard(),
            States::WaitingForInterests
        );
    }
}
