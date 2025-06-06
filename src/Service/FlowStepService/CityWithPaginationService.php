<?php

namespace App\Service\FlowStepService;

use App\DTO\Keyboard;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Service\FlowStepServiceInterface;
use App\Service\GeoDbService;
use App\Service\UserStateStorage;

readonly class CityWithPaginationService implements FlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public function __construct(
        private GeoDbService $geoDbService,
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::CityPage->value)
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);
        if (null === $context->country) {
            throw new \RuntimeException("Invalid payload");
        }

        $offset = (int) substr($update->callbackQuery->data, strlen(CallbackQueryData::CityPage->value));
        $cities = $this->geoDbService->getCitiesByCountry($context->country, $offset);
        $keyboard = $this->buildPaginationKeyboard(
            new Keyboard(
                $cities,
                CallbackQueryData::City->value,
                'name',
                'name',
                CallbackQueryData::CityPage->value,
                $offset + 5
            ),
        );

        return new SendMessageContext($update->callbackQuery->message->chat->id, "Ще 5 міст:", $keyboard);
    }
}
