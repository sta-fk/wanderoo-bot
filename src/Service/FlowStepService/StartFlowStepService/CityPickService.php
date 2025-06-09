<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\Place\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class CityPickService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
        private UserStateStorage      $userStateStorage,
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
            $this->getDurationKeyboard(),
            States::WaitingForDuration,
        );
    }

    private function getDurationKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [['text' => '1 день', 'callback_data' => CallbackQueryData::Duration->value.'1']],
                [['text' => '3 дні', 'callback_data' => CallbackQueryData::Duration->value.'3']],
                [['text' => '5 днів', 'callback_data' => CallbackQueryData::Duration->value.'5']],
                [['text' => '7 днів', 'callback_data' => CallbackQueryData::Duration->value.'7']],
                [['text' => '🔢 Інший варіант', 'callback_data' => CallbackQueryData::Duration->value.'custom']],
            ]
        ];
    }
}
