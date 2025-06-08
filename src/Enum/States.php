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

    case WaitingForStopCountry = 'waiting_for_stop_country';
    case WaitingForStopCity = 'waiting_for_stop_city';
    case WaitingForStopDuration = 'waiting_for_stop_duration';
    case WaitingForStopCustomDuration = 'waiting_for_stop_custom_duration';
    case WaitingForStopTripStyle = 'waiting_for_stop_trip_style';
    case WaitingForStopInterests = 'waiting_for_stop_interests';
    case WaitingForStopCustomBudget = 'waiting_for_stop_custom_budget';
    case WaitingForConfirmStop = 'waiting_confirm_stop';

}
