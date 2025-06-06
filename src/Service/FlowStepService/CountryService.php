<?php

namespace App\Service\FlowStepService;

use App\DTO\Keyboard;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\GeoDbService;
use App\Service\UserStateStorage;

class CountryService implements StatefulFlowStepServiceInterface
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
            && str_starts_with($update->callbackQuery->data, CallbackQueryData::Country->value)
            && !strpos($update->callbackQuery->data, 'page')
        ;
    }

    public function getNextState(): States
    {
        return States::WaitingForCity;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $countryCode = substr($update->callbackQuery->data, strlen(CallbackQueryData::CountryPage->value));
        $context->country = $countryCode;
        $this->userStateStorage->saveContext($chatId, $context);

        $cities = $this->geoDbService->getCitiesByCountry($countryCode);
        $keyboard = $this->buildPaginationKeyboard(
            new Keyboard(
                $cities,
                CallbackQueryData::City->value,
                'name',
                'name',
                CallbackQueryData::CityPage->value,
                5
            ),
        );

        return new SendMessageContext($update->callbackQuery->message->chat->id, "🚀Оберіть місто:", $keyboard);
    }

    // You have exceeded the rate limit per second for your plan, BASIC, by the API provider
    //    private function getMessageText(string $countryCode): string
    //    {
    //        $details = $this->geoDbService->getCountryDetails($countryCode);
    //
    //        $name = $details['name'];
    //        $capital = $details['capital'];
    //        $currency = $details['currency'];
    //
    //        $text = <<<TEXT
    //✅Ви обрали країну: $name. Столиця: $capital. Місцева валюта: $currency.
    //
    //🚀Оберіть місто:
    //TEXT;
    //
    //        return $text;
    //    }
}
