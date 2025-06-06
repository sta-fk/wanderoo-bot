<?php

namespace App\Enum;

enum States: string
{
    case WaitingForStart = 'waiting_for_start';
    case WaitingForCountry = 'waiting_for_country';
    case WaitingForCity = 'waiting_for_city';
    case WaitingForDuration = 'waiting_for_duration';
    case WaitingForCustomDuration = 'waiting_for_custom_duration';
    case WaitingForStartDate = 'waiting_for_start_date';
    case WaitingForTripStyle = 'waiting_for_trip_style';
    case WaitingForInterests = 'waiting_for_interests';
    case WaitingForBudget = 'waiting_for_budget';
    case WaitingForCustomBudget = 'waiting_for_custom_budget';
    case ReadyToBuildPlan = 'ready_to_build_plan';
}
