<?php

namespace App\Enum;

enum States: string
{
    // Start flow
    case WaitingForStart = 'waiting_for_start';
    case WaitingForCountry = 'waiting_for_country';
    case WaitingForCountryPick = 'waiting_for_country_pick';
    case WaitingForCitySearch = 'waiting_for_city_search';
    case WaitingForCityPick = 'waiting_for_city_pick';
    case WaitingForDuration = 'waiting_for_duration';
    case WaitingForCustomDuration = 'waiting_for_custom_duration';
    case WaitingForStartDate = 'waiting_for_start_date';
    case WaitingForTripStyle = 'waiting_for_trip_style';
    case WaitingForInterests = 'waiting_for_interests';
    case WaitingForBudget = 'waiting_for_budget';
    case WaitingForCustomBudget = 'waiting_for_custom_budget';
    case ReadyToBuildPlan = 'ready_to_build_plan';

    // Add stop flow
    case WaitingForStopCountry = 'waiting_for_stop_country';
    case WaitingForReuseOrNewTripStyle = 'waiting_for_reuse_or_new_trip_style';
    case WaitingForReuseOrNewInterests = 'waiting_for_reuse_or_new_interests';
}
