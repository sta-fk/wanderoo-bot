<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\EnterCountryNameViewData;
use App\DTO\Internal\StartNewViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\Context\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackData;
use App\Enum\States;
use App\Service\FlowStepService\StateAwareFlowViewDataServiceInterface;
use App\Service\Integrations\PlaceServiceInterface;
use App\Service\UserStateStorage;

readonly class EnterCountryNameDataService implements StateAwareFlowViewDataServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
        private PlaceServiceInterface $placeService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->message;
    }

    public function supportsStates(): array
    {
        return [States::WaitingForCountryName];
    }

    public function buildNextStepViewData(TelegramUpdate $update): ViewDataInterface
    {
        $chatId = $update->message->chat->id;
        $countries = $this->placeService->searchCountries($update->message->text);

        if (empty($countries)) {
            return new EnterCountryNameViewData($chatId, []);
                // new SendMessageContext($chatId, "Не знайдено такої країни. Спробуйте ще раз.");
        }

        $nextStateKeyboardProvider = $this->keyboardProviderResolver->resolve(States::WaitingForCountryPick);

        return new SendMessageContext(
            $chatId,
            $nextStateKeyboardProvider->getTextMessage(),
            $nextStateKeyboardProvider->buildKeyboard($countries),
            States::WaitingForCountryPick
        );

        return new StartNewViewData($update->message->chat->id);
    }
}
