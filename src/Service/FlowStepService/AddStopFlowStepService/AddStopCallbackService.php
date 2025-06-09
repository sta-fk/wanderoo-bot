<?php

namespace App\Service\FlowStepService\AddStopFlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\DTO\StopContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\FlowStepServiceInterface;
use App\Service\KeyboardService\BuildGeneralKeyboardTrait;
use App\Service\UserStateStorage;

readonly class AddStopCallbackService implements FlowStepServiceInterface
{
    use BuildGeneralKeyboardTrait;

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return null !== $update->callbackQuery
            && $update->callbackQuery->data === CallbackQueryData::AddStop->value
        ;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->callbackQuery->message->chat->id;

        $context = $this->userStateStorage->getContext($chatId);

        $lastOneCountryName = null;
        if (null !== $context->currentStopDraft) {
            $lastOneCountryName = $context->currentStopDraft->countryName;
            $context->stops[] = $context->currentStopDraft;
        }

        $context->currentStopDraft = new StopContext();
        $this->userStateStorage->saveContext($chatId, $context);

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => "✅ Хочу ще в іншу країну", 'callback_data' => CallbackQueryData::StopCountryAnother->value],
                ],
                [
                    ['text' => "❌ Ні, продовжу подорож в <b>{$lastOneCountryName}</b>", 'callback_data' => CallbackQueryData::StopCountrySame->value],
                ],
            ],
        ];

        $text = null !== $lastOneCountryName
            ? "Ви бажаєте відвідати ще якісь міста в {$lastOneCountryName}? Або бажаєте відвідати ще одну країну?"
            : "Чи бажаєте ще в іншу країну?";


        return new SendMessageContext(
            $chatId,
            $text,
            $keyboard,
            States::WaitingForStopCountry
        );
    }
}
