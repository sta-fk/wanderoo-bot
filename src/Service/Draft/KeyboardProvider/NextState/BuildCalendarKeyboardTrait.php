<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;

trait BuildCalendarKeyboardTrait
{
    private function buildCalendarKeyboard(int $year, int $month): array
    {
        $keyboard = [];

        // 1️⃣ Рядок з місяцем + роком
        $monthName = ucfirst(strftime('%B', strtotime("$year-$month-01"))); // Наприклад "Червень"
        $keyboard[] = [[
            'text' => "📅 $monthName $year",
            'callback_data' => 'ignore',
        ]];

        // 2️⃣ Рядок з днями тижня
        $daysOfWeek = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Нд'];
        $dayOfWeekButtons = [];

        foreach ($daysOfWeek as $dayName) {
            $dayOfWeekButtons[] = [
                'text' => $dayName,
                'callback_data' => 'ignore',
            ];
        }

        $keyboard[] = $dayOfWeekButtons;

        // 3️⃣ Дні місяця
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDayOfMonth);
        $startDayOfWeek = (date('N', $firstDayOfMonth) - 1); // 0 (Пн) .. 6 (Нд)

        $row = [];

        // Порожні кнопки перед першим днем місяця
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $row[] = [
                'text' => '◾️',
                'callback_data' => 'ignore',
            ];
        }

        // Дні місяця
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

            $row[] = [
                'text' => (string)$day,
                'callback_data' => CallbackQueryData::PickDate->value . $date,
            ];

            // Якщо кінець тижня — завершуємо рядок
            if (count($row) === 7) {
                $keyboard[] = $row;
                $row = [];
            }
        }

        // Додати останній неповний рядок, якщо залишився
        if (!empty($row)) {
            // Заповнюємо порожніми клітинками до 7
            while (count($row) < 7) {
                $row[] = [
                    'text' => '◾️',
                    'callback_data' => 'ignore',
                ];
            }
            $keyboard[] = $row;
        }

        // 4️⃣ Рядок з кнопками навігації
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth === 0) {
            $prevMonth = 12;
            $prevYear--;
        }

        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth === 13) {
            $nextMonth = 1;
            $nextYear++;
        }

        $keyboard[] = [
            [
                'text' => '◀️',
                'callback_data' => CallbackQueryData::Calendar->value . "{$prevYear}_" . sprintf('%02d', $prevMonth),
            ],
            [
                'text' => '▶️',
                'callback_data' => CallbackQueryData::Calendar->value . "{$nextYear}_" . sprintf('%02d', $nextMonth),
            ],
        ];

        return ['inline_keyboard' => $keyboard];
    }
}
