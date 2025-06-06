<?php

namespace App\Service\FlowStepService;

use App\DTO\Keyboard;

trait BuildKeyboardTrait
{
    private function buildPaginationKeyboard(Keyboard $keyboard): array
    {
        $buttons = [];

        foreach ($keyboard->items as $item) {
            $buttons[][] = [
                'text' => $item[$keyboard->textField],
                'callback_data' => $keyboard->prefix . $item[$keyboard->keyField],
            ];
        }

        if ($keyboard->nextPageOffset !== null) {
            $buttons[][] = [
                'text' => '➡️ Наступна сторінка',
                'callback_data' => $keyboard->paginationPrefix . $keyboard->nextPageOffset,
            ];
        }

        return ['inline_keyboard' => $buttons];
    }

    private function buildKeyboard(Keyboard $keyboard): array
    {
        $buttons = [];
        foreach ($keyboard->items as $item) {
            $buttons[][] = [
                'text' => $item[$keyboard->textField],
                'callback_data' => $keyboard->prefix . $item[$keyboard->keyField],
            ];
        }
        return ['inline_keyboard' => $buttons];
    }

    private function buildCalendarKeyboard(int $year, int $month): array
    {
        $date = new \DateTimeImmutable("$year-$month-01");
        $daysInMonth = (int)$date->format('t');
        $startDayOfWeek = (int)$date->format('N'); // 1 (Mon) – 7 (Sun)

        $buttons = [];
        $week = [];

        // Пусті клітинки перед першим днем
        for ($i = 1; $i < $startDayOfWeek; $i++) {
            $week[] = ['text' => ' ', 'callback_data' => 'ignore'];
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $week[] = [
                'text' => (string)$day,
                'callback_data' => "pick_date_$dateStr"
            ];

            if (count($week) === 7) {
                $buttons[] = $week;
                $week = [];
            }
        }

        // Додаємо останній тиждень
        if (count($week) > 0) {
            while (count($week) < 7) {
                $week[] = ['text' => ' ', 'callback_data' => 'ignore'];
            }
            $buttons[] = $week;
        }

        // Кнопки навігації
        $prevMonth = (new \DateTimeImmutable("$year-$month-01"))->modify('-1 month');
        $nextMonth = (new \DateTimeImmutable("$year-$month-01"))->modify('+1 month');

        $buttons[] = [
            [
                'text' => '◀️',
                'callback_data' => "calendar_{$prevMonth->format('Y_m')}"
            ],
            [
                'text' => '▶️',
                'callback_data' => "calendar_{$nextMonth->format('Y_m')}"
            ]
        ];

        return $buttons;
    }
}
