<?php

namespace App\Enum;

enum States: string
{
    // Start flow
    case WaitingForStartNew = 'waiting_for_start_new';
    case WaitingForCountryName = 'waiting_for_country_name';
    case WaitingForCountryPick = 'waiting_for_country_pick';
}
