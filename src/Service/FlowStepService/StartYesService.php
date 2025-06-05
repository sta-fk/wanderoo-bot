<?php

namespace App\Service\FlowStepService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Enum\TelegramButtons;
use App\Service\GeoDbService;
use App\Service\UserStateStorage;

readonly class StartYesService implements FlowStepServiceInterface
{
    public function __construct(
        private GeoDbService $geoDbService,
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery && TelegramButtons::StartYes->value === $update->callbackQuery->data;
    }

    public function getNextState(): States
    {
        return States::WaitingForContinent;
    }

    public function buildMessage(TelegramUpdate $update): SendMessageContext
    {
        $this->userStateStorage->saveContext($update->callbackQuery->message->chat->id, new PlanContext());

        $continents = $this->geoDbService->getContinents();
        $keyboard = $this->buildKeyboard($continents);

        return new SendMessageContext($update->message->chat->id, 'Супер, поїхали ✨! Обери континент:', $keyboard);
    }

    private function buildKeyboard(array $items): array
    {
        $buttons = [];
        foreach ($items as $item) {
            $buttons[][] = [
                'text' => $item['name'],
                'callback_data' => 'continent_'.$item['code'],
            ];
        }

        return ['inline_keyboard' => $buttons];
    }
}
