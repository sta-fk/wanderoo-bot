<?php

namespace App\Enum;

enum States: string
{
    case WaitingForStart = 'waiting_for_start';
    case WaitingForCountry = 'waiting_for_country';
    case WaitingForCity = 'waiting_for_city';
    case ReadyForDates = 'ready_for_dates';
}
