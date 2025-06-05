<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Service\GeoDbService;
use App\Service\UserStateStorage;

class ContinentService implements FlowStepServiceInterface
{
    private const CALLBACK_QUERY_DATA_STARTS_WITH = 'continent_';

    public function __construct(
        private readonly GeoDbService $geoDbService,
        private readonly UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && str_starts_with($update->callbackQuery->data, self::CALLBACK_QUERY_DATA_STARTS_WITH);
    }

    public function getNextState(): States
    {
        return States::WaitingForCountry;
    }

    public function buildMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $continentCode = substr($update->callbackQuery->data, 10);
        $context->continent = $continentCode;
        $this->userStateStorage->saveContext($chatId, $context);

        $countries = $this->geoDbService->getCountriesByContinent($continentCode);
        $keyboard = $this->buildKeyboard($countries);

        return new SendMessageContext($update->message->chat->id, 'Оберіть країну:', $keyboard);
    }

    private function buildKeyboard(array $items): array
    {
        $buttons = [];
        foreach ($items as $item) {
            $buttons[][] = [
                'text' => $item['name'],
                'callback_data' => 'country_'.$item['code'],
            ];
        }

        return ['inline_keyboard' => $buttons];
    }
}
