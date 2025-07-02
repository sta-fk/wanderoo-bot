<?php

namespace App\Service\TelegramViewer;

use App\DTO\Context\PlanContext;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewCurrentDraftPlanViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ViewCurrentDraftPlanViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::ViewCurrentDraftPlan);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ViewCurrentDraftPlanViewData);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->buildMessage($data->planContext),
            replyMarkup: ['inline_keyboard' => $this->buildKeyboard($data->planContext)],
        );
    }

    private function buildMessage(PlanContext $context): string
    {
        if (empty($context->stops)) {
            return "🚧 Ваш план подорожі поки що порожній.";
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

    private function buildKeyboard(PlanContext $context): array
    {
        if (empty($context->stops)) {
            return [[[
                'text' => $this->translator->trans('menu.keyboard.start_new'),
                'callback_data' => CallbackQueryData::StartNew->value
            ]]];
        }

        if (null !== $context->currentStopDraft?->countryName) {
            return [
                    [[
                        'text' => $this->translator->trans('commands.view_saved.details.edit'),
                        'callback_data' => CallbackQueryData::EditGeneratedPlan->value
                    ]],
                    [[
                        'text' => $this->translator->trans('trip.plan_generated.start_over'),
                        'callback_data' => CallbackQueryData::StartNew->value
                    ]],
            ];
        }

        return [
            [[
                'text' => $this->translator->trans('trip.context.finished.keyboard.generate_plan'),
                'callback_data' => CallbackQueryData::GeneratingTripPlan->value
            ]],
            [
                ['text' => $this->translator->trans('commands.view_saved.details.edit'), 'callback_data' => CallbackQueryData::EditGeneratedPlan->value],
                ['text' => $this->translator->trans('trip.context.finished.keyboard.add_stop'), 'callback_data' => CallbackQueryData::AddStop->value],
            ],
            [
                ['text' => $this->translator->trans('trip.plan_generated.start_over'), 'callback_data' => CallbackQueryData::StartNew->value],
                ['text' => $this->translator->trans('trip.context.finished.keyboard.exchanger'), 'callback_data' => CallbackQueryData::DraftPlanCurrency->value],
            ]
        ];
    }
}
