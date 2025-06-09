<?php

namespace App\Service\CommandsService;

use App\DTO\PlanContext;
use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\CallbackQueryData;
use App\Enum\TelegramCommands;
use App\Service\FlowStepServiceInterface;
use App\Service\UserStateStorage;

readonly class ViewTripService implements FlowStepServiceInterface
{
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
        $text = $this->buildViewTripMessage($context);
        $keyboard = $this->getViewTripKeyboard();

        return new SendMessageContext($chatId, $text, $keyboard);
    }

    public function buildViewTripMessage(PlanContext $context): string
    {
        if (empty($context->stops)) {
            return "🚧 Ваш план подорожі поки що порожній.\nДодайте першу зупинку за допомогою /start або /add_stop!";
        }

        $lines = [];

        // Заголовок плану
        $lines[] = "🗺️ <b>Ваш план подорожі</b>";
        if ($context->planName) {
            $lines[] = "Назва плану: <b>{$context->planName}</b>";
        }

        // Дати та загальна тривалість
        if ($context->startDate && $context->endDate) {
            $startDateStr = $context->startDate->format('d.m.Y');
            $endDateStr = $context->endDate->format('d.m.Y');
            $lines[] = "📅 Дати: <b>{$startDateStr}</b> - <b>{$endDateStr}</b>";
        }

        if ($context->totalDuration !== null) {
            $lines[] = "⏳ Загальна тривалість: <b>{$context->totalDuration} днів</b>";
        }

        // Загальний бюджет + валюта
        if ($context->budget !== null && $context->currency !== null) {
            $lines[] = "💰 Загальний бюджет: <b>{$context->budget} {$context->currency}</b>";
        } elseif ($context->budget !== null) {
            $lines[] = "💰 Загальний бюджет: <b>{$context->budget}</b>";
        }

        $lines[] = ""; // порожній рядок для відступу

        // Перелік зупинок
        foreach ($context->stops as $index => $stop) {
            $stopNumber = $index + 1;
            $lines[] = "📍 <b>Зупинка {$stopNumber}</b>";

            if ($stop->countryName && $stop->cityName) {
                $lines[] = "🌍 <b>{$stop->countryName}</b> → 🏙️ <b>{$stop->cityName}</b>";
            } elseif ($stop->cityName) {
                $lines[] = "🏙️ <b>{$stop->cityName}</b>";
            } elseif ($stop->countryName) {
                $lines[] = "🌍 <b>{$stop->countryName}</b>";
            }

            if ($stop->duration !== null) {
                $lines[] = "⏳ Тривалість: <b>{$stop->duration} днів</b>";
            }

            if ($stop->tripStyle !== null) {
                $lines[] = "🎒 Стиль подорожі: <b>{$stop->tripStyle}</b>";
            }

            if (!empty($stop->interests)) {
                $interestsStr = implode(', ', $stop->interests);
                $lines[] = "🎯 Інтереси: <b>{$interestsStr}</b>";
            }

            if ($stop->budget !== null) {
                $lines[] = "💸 Бюджет на зупинку: <b>{$stop->budget}</b>";
            }

            $lines[] = ""; // порожній рядок між зупинками
        }

        return implode("\n", $lines);
    }

    private function getViewTripKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '➕ Додати зупинку', 'callback_data' => CallbackQueryData::AddStop->value],
                ],
                [
                    ['text' => '✅ Завершити планування', 'callback_data' => CallbackQueryData::ReadyToBuildPlan->value],
                ],
                [
                    ['text' => '🔄 Почати заново', 'callback_data' => CallbackQueryData::NewTrip->value],
                ],
            ]
        ];
    }
}
