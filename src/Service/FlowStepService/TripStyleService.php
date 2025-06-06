<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class TripStyleService implements StateAwareFlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, CallbackQueryData::TripStyle->value);
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
        $context->tripStyle = $tripStyle;

        $this->userStateStorage->saveContext($chatId, $context);

        $keyboard = $this->buildInterestsKeyboard($context->interests, InterestsService::INTERESTS);

        return new SendMessageContext(
            $chatId,
            "Ви обрали стиль подорожі: <b>{$tripStyle}</b>.\n\nНаступний крок...\n\n✨ Що вас цікавить у подорожі? Оберіть кілька варіантів:",
            $keyboard,
            States::WaitingForInterests
        );
    }
}
