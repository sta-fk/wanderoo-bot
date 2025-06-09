<?php

namespace App\Service\CommandsService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class AddStopService implements FlowStepServiceInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(TelegramUpdate $update): bool
    {
        return $update->message?->text === TelegramCommands::AddStop->value;
    }

    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext
    {
        $chatId = $update->message->chat->id;

        $context = $this->userStateStorage->getContext($chatId);

        if (null === $context->currentStopDraft) {
            $text = "В тебе немає поточної поїздки \n\n Почнемо?";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '🧳 Так, хочу план!', 'callback_data' => CallbackQueryData::StartYes->value],
                        ['text' => '❌ Ні, просто дивлюсь', 'callback_data' => CallbackQueryData::StartNo->value],
                    ],
                ],
            ];

            return new SendMessageContext(
                $chatId,
                $text,
                $keyboard,
                States::WaitingForStart
            );
        }

        $lastOneCountryName = $context->currentStopDraft->countryName;
        $context->saveLastStopDraft();
        $context->resetCurrentStopDraft();
        $context->enableAddingStopFlow();

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
