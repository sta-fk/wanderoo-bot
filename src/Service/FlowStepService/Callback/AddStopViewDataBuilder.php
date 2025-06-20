<?php

namespace App\Service\FlowStepService\Callback;

use App\DTO\Internal\AddStopViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\UserStateStorage;

readonly class AddStopViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && CallbackQueryData::AddStop->value === $update->callbackQuery->data;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->callbackQuery->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        $viewDataCollection = ViewDataCollection::createStateAwareWithSingleViewData(
            new AddStopViewData(
                $chatId,
                ($context->stops[count($context->stops) - 1])->countryName
            ),
            States::WaitingForStopCountry
        );

        $context->enableAddingStopFlow();

        $this->userStateStorage->saveContext($chatId, $context);

        return $viewDataCollection;
    }
}
