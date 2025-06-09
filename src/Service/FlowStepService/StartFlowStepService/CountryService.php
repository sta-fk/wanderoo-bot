<?php

namespace App\Service\FlowStepService\StartFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowStepServiceInterface;
use App\Service\Place\PlaceServiceInterface;

readonly class CountryService implements StateAwareFlowStepServiceInterface
{
    public function __construct(
        private PlaceServiceInterface $placeService,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->message
            && null !== $update->message->text;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCountry];
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $countries = $this->placeService->searchCountries($update->message->text);

        if (empty($countries)) {
            return new SendMessageContext($chatId, "Не знайдено такої країни. Спробуйте ще раз.");
        }

        $keyboard = [];
        foreach ($countries as $country) {
            $keyboard[] = [
                [
                    'text' => $country->name,
                    'callback_data' => CallbackQueryData::Country->value . $country->placeId,
                ],
            ];
        }

        return new SendMessageContext($chatId, "Оберіть країну:", ['inline_keyboard' => $keyboard], States::WaitingForCountryCity);
    }
}
