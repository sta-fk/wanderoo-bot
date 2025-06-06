<?php

namespace App\Service\FlowStepService;

use App\DTO\Keyboard;
use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use App\Enum\CallbackQueryData;
use App\Service\GeoDbService;
use App\Service\UserStateStorage;

readonly class StartYesService implements StatefulFlowStepServiceInterface
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
            && CallbackQueryData::StartYes->value === $update->callbackQuery->data
        ;
    }

    public function getNextState(): States
    {
        return States::WaitingForCountry;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $this->userStateStorage->saveContext($update->callbackQuery->message->chat->id, new PlanContext());

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

        return new SendMessageContext($update->callbackQuery->message->chat->id, "Супер, поїхали ✨! Обери країну:", $keyboard);
    }
}
