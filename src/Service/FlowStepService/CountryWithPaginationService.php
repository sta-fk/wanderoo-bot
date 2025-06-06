<?php

namespace App\Service\FlowStepService;

use App\DTO\Keyboard;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Service\GeoDbService;
use App\Service\UserStateStorage;

class CountryWithPaginationService implements FlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public function __construct(
        private readonly GeoDbService $geoDbService,
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::CountryPage->value)
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $offset = (int) substr($update->callbackQuery->data, strlen(CallbackQueryData::CountryPage->value));
        $countries = $this->geoDbService->getCountries($offset);
        $keyboard = $this->buildPaginationKeyboard(
            new Keyboard(
                $countries,
                CallbackQueryData::Country->value,
                'name',
                'code',
                CallbackQueryData::CountryPage->value,
                $offset + 5
            ),
        );

        return new SendMessageContext($update->callbackQuery->message->chat->id, "Ще 5 країн:", $keyboard);
    }
}
