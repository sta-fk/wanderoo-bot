<?php

namespace App\Service\CommandsService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardService\GetViewTripKeyboardTrait;
use App\Service\UserStateStorage;

readonly class ViewTripService implements FlowStepServiceInterface
{
    use GetViewTripKeyboardTrait;

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text === TelegramCommands::ViewTrip->value;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;
        $context = $this->userStateStorage->getContext($chatId);

        if (null === $context->tripStyle) {
            return new SendMessageContext($chatId, 'У вас немає створеної подорожі. Створіть нову за допомогою команди ' . TelegramCommands::NewTrip->value . '.');
        }

        $text = $this->getViewPlan($context);
        $keyboard = $this->getViewTripKeyboard();

        return new SendMessageContext($chatId, $text, $keyboard);
    }
}

