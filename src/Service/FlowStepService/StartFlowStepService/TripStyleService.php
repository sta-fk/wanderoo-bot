<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\KeyboardService\BuildInterestsKeyboardTrait;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class TripStyleService implements StateAwareFlowStepServiceInterface
{
    use BuildInterestsKeyboardTrait;

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::TripStyle->value)
        ;
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
            $keyboard = [
                'inline_keyboard' => [
                    ['✅ Так', CallbackQueryData::Interest->value . CallbackQueryData::Reuse->value],
                    ['❌ Ні', CallbackQueryData::Interest->value . CallbackQueryData::New->value],
                ]
            ];

            return new SendMessageContext(
                $chatId,
                "Стиль подорожі для {$context->currentStopDraft->cityName}: <b>{$context->currentStopDraft->tripStyle}</b>.\n\nНаступний крок...\n\n✨ Використати попередні інтереси для цієї зупинки?",
                $keyboard,
                States::WaitingForReuseOrNewInterests
            );
        }

        $keyboard = $this->buildInterestsKeyboard(
            $context->currentStopDraft->interests,
            InterestsService::INTERESTS
        );

        return new SendMessageContext(
            $chatId,
            "Ви обрали стиль подорожі: <b>{$context->currentStopDraft->tripStyle}</b>.\n\nНаступний крок...\n\n✨ Що вас цікавить у подорожі? Оберіть кілька варіантів:",
            $keyboard,
            States::WaitingForInterests
        );
    }
}
