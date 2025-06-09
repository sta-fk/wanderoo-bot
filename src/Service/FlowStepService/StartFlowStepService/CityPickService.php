<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\KeyboardService\GetDurationKeyboardTrait;
use App\Service\Place\PlaceServiceInterface;
use App\Service\UserStateStorage;

class CityPickService implements StateAwareFlowStepServiceInterface
{
    use GetDurationKeyboardTrait;

    public function __construct(
        private readonly PlaceServiceInterface $placeService,
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::City->value);
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCityPick];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $cityPlaceId = substr($update->callbackQuery->data, strlen(CallbackQueryData::City->value));

        $cityDetails = $this->placeService->getPlaceDetails($cityPlaceId);

        $context->currentStopDraft->cityName = $cityDetails->name;
        $context->currentStopDraft->cityPlaceId = $cityPlaceId;

        $this->userStateStorage->saveContext($chatId, $context);

        return $this->getSendMessageContext($chatId, $context);
    }

    private function getSendMessageContext(int $chatId, PlanContext $context): SendMessageContext
    {
        if ($context->isAddingStopFlow) {
            return new SendMessageContext(
                $chatId,
                "Введіть кількість днів (наприклад, 4):",
                null,
                States::WaitingForCustomDuration
            );
        }

        return new SendMessageContext(
            $chatId,
            "Чудово! Тепер оберіть тривалість перебування у місті (днів):",
            $this->getDurationKeyboard(CallbackQueryData::Duration),
            States::WaitingForDuration,
        );
    }
}
