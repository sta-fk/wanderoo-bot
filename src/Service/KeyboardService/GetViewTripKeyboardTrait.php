<?php

namespace App\Service\KeyboardService;

use App\DTO\PlanContext;
use App\Enum\CallbackQueryData;

trait GetViewTripKeyboardTrait
{
    private function getViewPlan(PlanContext $context): string
    {
        $summaryLines = [];
        $summaryLines[] = "🌍 <b>Ваш план подорожі:</b>\n";

        if ($context->tripStyle) {
            $summaryLines[] = "🗺️ <b>Стиль подорожі:</b> {$context->tripStyle}";
        }

        if (!empty($context->interests)) {
            $summaryLines[] = "🎭 <b>Інтереси:</b> " . implode(', ', $context->interests);
        }

        if ($context->budget) {
            $summaryLines[] = "💰 <b>Бюджет:</b> {$context->budget}";
        }

        if ($context->duration) {
            $summaryLines[] = "🕑 <b>Тривалість:</b> {$context->duration} днів";
        }

        if ($context->startDate) {
            $summaryLines[] = "📅 <b>Дата початку:</b> {$context->startDate}";
        }

        // Зупинки
        if (!empty($context->stops)) {
            $summaryLines[] = "\n📍 <b>Зупинки:</b>";
            foreach ($context->stops as $index => $stop) {
                $stopNumber = $index + 1;
                $summaryLines[] = "{$stopNumber}️⃣ {$stop->country} — {$stop->city} ({$stop->duration} днів)";
            }
        } else {
            $summaryLines[] = "\n📍 <b>Зупинок немає.</b>";
        }

        $summaryLines[] = "\n✅ Коли будете готові, натисніть <b>\"Завершити планування\"</b> або додайте ще зупинку.";

        return implode("\n", $summaryLines);
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
                    ['text' => '🔄 Почати заново', 'callback_data' => CallbackQueryData::StartYes->value],
                ],
            ]
        ];
    }
}
