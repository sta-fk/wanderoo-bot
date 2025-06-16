<?php

namespace App\Service\Draft\CommandsService;

use App\DTO\Context\PlanContext;
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

    private function buildViewTripMessage(PlanContext $context): string
    {
        if (empty($context->stops)) {
            return "🚧 Ваш план подорожі поки що порожній.\nДодайте першу зупинку за допомогою /start або /add_stop!";
        }

        $lines = [];

        if ($context->planName) {
            $lines[] = "📍 <b>{$context->planName}</b>";
        }

        if ($context->startDate && $context->endDate) {
            $lines[] = "📅 <b>{$context->startDate->format('d.m.Y')}</b> — <b>{$context->endDate->format('d.m.Y')}</b>";
        }

        if ($context->currency) {
            $lines[] = "💱 Валюта плану: <b>{$context->currency}</b>";
        }

        if ($context->totalBudget) {
            $lines[] = "💰 Загальний бюджет: <b>{$context->totalBudget} {$context->currency}</b>";
        }

        $lines[] = "\n<b>Зупинки:</b>";

        foreach ($context->stops as $i => $stop) {
            $stopLines = [];

            $stopLines[] = "🔹 <b>" . ($stop->cityName ?? 'Місто?') . "</b>, " . ($stop->countryName ?? 'Країна?');

            if ($stop->duration) {
                $stopLines[] = "   🕒 Днів: {$stop->duration}";
            }

            if ($stop->tripStyle) {
                $stopLines[] = "   🎒 Стиль: {$stop->getTripStyleLabel()}";
            }

            if (!empty($stop->interests)) {
                $interests = implode(', ', $stop->getInterestsLabels());
                $stopLines[] = "   🧭 Інтереси: {$interests}";
            }

            if ($stop->budget !== null && $stop->budget !== 'none') {
                $budgetLine = "   💵 Бюджет: {$stop->budget} " . ($stop->currency ?? $context->currency);

                if (
                    $context->currency
                    && isset($stop->budgetInPlanCurrency)
                    && $stop->currency !== $context->currency
                ) {
                    $budgetLine .= " (~{$stop->budgetInPlanCurrency} {$context->currency})";
                }

                $stopLines[] = $budgetLine;
            }

            $lines[] = implode("\n", $stopLines);
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
