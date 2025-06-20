<?php

namespace App\Service\TripPlanner;

use App\DTO\TripPlan\DayPlan;
use App\DTO\TripPlan\StopPlan;
use App\DTO\TripPlan\TripPlan;
use IntlDateFormatter;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class HtmlTripPlanFormatter implements TripPlanFormatterInterface
{
    private const TELEGRAM_LIMIT = 4096;

    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function format(TripPlan $plan): string
    {
        return implode("\n\n", $this->splitFormattedPlan($plan));
    }

    public function splitFormattedPlan(TripPlan $plan, string $locale = 'uk'): array
    {
        $this->translator->setLocale($locale);

        $messages = [];
        $currentMessage = $this->renderHeader($plan, $locale);

        foreach ($plan->stops as $index => $stop) {
            $separator = $this->t('trip_plan.stop_separator');
            $stopHeader = $separator . "\n\n" . $this->renderStopHeader($stop, $index, $locale);
            $dayBlocks = [];

            foreach ($stop->days as $i => $day) {
                $dayBlocks[] = $this->renderDay($day, $i + 1);
            }

            $stopChunks = $this->splitIntoChunks($stopHeader, $dayBlocks);

            foreach ($stopChunks as $chunk) {
                $chunkText = $stopHeader . "\n\n" . implode("\n\n", $chunk);

                if (mb_strlen($currentMessage . "\n\n" . $chunkText) > self::TELEGRAM_LIMIT) {
                    $messages[] = $currentMessage;
                    $currentMessage = $chunkText;
                } else {
                    $currentMessage .= "\n\n" . $chunkText;
                }
            }
        }

        if (trim($currentMessage)) {
            $messages[] = $currentMessage;
        }

        return $messages;
    }

    private function renderHeader(TripPlan $plan, string $locale): string
    {
        $start = $this->formatDate($plan->startDate, $locale);
        $end = $this->formatDate($plan->endDate, $locale);

        return $this->t('trip_plan.header', [
            'title' => null !== $plan->name ? htmlspecialchars($plan->name) : $this->translator->trans('trip_plan.absent_title'),
            'startDate' => $start,
            'endDate' => $end,
            'totalBudget' => $plan->totalBudget,
            'currency' => $plan->currency,
            'totalDuration' => $plan->totalDuration,
        ]);
    }

    private function renderStopHeader(StopPlan $stop, int $index, string $locale): string
    {
        $start = $this->formatDate($stop->startDate, $locale);
        $end = $this->formatDate($stop->endDate, $locale);

        return $this->t('trip_plan.stop_header', [
            'index' => $index + 1,
            'city' => htmlspecialchars($stop->cityName),
            'country' => htmlspecialchars($stop->countryName),
            'startDate' => $start,
            'endDate' => $end,
        ]);
    }

    private function renderDay(DayPlan $day, int $dayIndex): string
    {
        $text = $this->t('trip_plan.day_title', ['index' => $dayIndex]);

        $text .= "\n" . $this->t('trip_plan.activities_title');
        if (empty($day->activities)) {
            $text .= "\n" . $this->t('trip_plan.free_time');
        } else {
            foreach ($day->activities as $activity) {
                $text .= "\n• " . htmlspecialchars($activity);
            }
        }

        $text .= "\n\n" . $this->t('trip_plan.food_title');
        if (empty($day->foodPlaces)) {
            $text .= "\n" . $this->t('trip_plan.no_food');
        } else {
            foreach ($day->foodPlaces as $food) {
                $text .= "\n• " . htmlspecialchars($food);
            }
        }

        return $text;
    }

    private function splitIntoChunks(string $header, array $dayBlocks): array
    {
        $chunks = [];
        $current = [];
        $currentLen = mb_strlen($header);

        foreach ($dayBlocks as $block) {
            $blockLen = mb_strlen($block) + 2;
            if ($currentLen + $blockLen > self::TELEGRAM_LIMIT) {
                if (!empty($current)) {
                    $chunks[] = $current;
                }
                $current = [$block];
                $currentLen = mb_strlen($header) + $blockLen;
            } else {
                $current[] = $block;
                $currentLen += $blockLen;
            }
        }

        if (!empty($current)) {
            $chunks[] = $current;
        }

        return $chunks;
    }

    private function t(string $key, array $params = []): string
    {
        $convertedParams = [];
        foreach ($params as $id => $value) {
            $convertedParams['{' . $id . '}'] = $value;
        }

        return $this->translator->trans($key, $convertedParams, 'messages', $this->translator->getLocale());
    }

    private function formatDate(\DateTimeInterface $date, string $locale): string
    {
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'd MMMM');
        return $formatter->format($date);
    }
}
