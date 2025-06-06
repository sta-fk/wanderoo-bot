<?php

namespace App\Service\CommandsService;

use App\DTO\Keyboard;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Service\FlowStepService\BuildKeyboardTrait;
use App\Service\FlowStepServiceInterface;
use App\Service\GeoDbService;
use App\Service\UserStateStorage;

class NewTripService implements FlowStepServiceInterface
{
    use BuildKeyboardTrait;

    public function __construct(
        private readonly UserStateStorage $userStateStorage,
        private readonly GeoDbService $geoDbService,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text === TelegramCommands::NewTrip->value;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;

        $this->userStateStorage->clearContext($chatId);

        $countries = $this->geoDbService->getCountries();
        $keyboard = $this->buildPaginationKeyboard(
            new Keyboard(
                $countries,
                CallbackQueryData::Country->value,
                'name',
                'code',
                CallbackQueryData::CountryPage->value,
                5
            ),
        );

        return new SendMessageContext(
            $chatId,
            "Розпочнімо нову подорож! 🌍\n\nСпершу оберіть країну, яку хочете відвідати:",
            $keyboard,
            States::WaitingForCountry
        );
    }
}
