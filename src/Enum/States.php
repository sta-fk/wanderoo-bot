<?php

namespace App\Enum;

enum States: string
{
    // Start flow
    case WaitingForStartNew = 'waiting_for_start_new';
    case WaitingForCountryInput = 'waiting_for_country_input';
    case WaitingForCountryPicked = 'waiting_for_country_picked';
    case WaitingForCityInput = 'waiting_for_city_input';
    case WaitingForCityPicked = 'waiting_for_city_picked';
    case WaitingForDurationPicked = 'waiting_for_duration';
    case WaitingForCustomDurationInput = 'waiting_for_custom_duration_input';
    case WaitingForStartDate = 'waiting_for_start_date';
    case WaitingForDatePicked = 'waiting_for_date_picked';
    case WaitingForTripStyle = 'waiting_for_trip_style';
    case WaitingForInterests = 'waiting_for_interests';
    case WaitingForBudget = 'waiting_for_budget';
    case WaitingForCustomBudgetInput = 'waiting_for_custom_budget';
    case TripStopCreationFinished = 'trip_stop_creation_finished';
    case PlanGenerationFinished = 'plan_generation_finished';

    // Adding Stop flow
    case WaitingForStopCountry = 'waiting_for_stop_country';
    case WaitingForReuseOrNewTripStyle = 'waiting_for_reuse_or_new_trip_style';
    case WaitingForReuseOrNewInterests = 'waiting_for_reuse_or_new_interests';
    case WaitingForCurrencyChoicePicked = 'waiting_for_currency_choice';
    case WaitingForCurrencyCountryInput = 'waiting_for_currency_country_input';
    case WaitingForCurrencyPicked = 'waiting_for_currency_picked';

    // Exchange
    case WaitingForExchangeChoicePicked = 'waiting_for_exchange_choice_picked';
    case WaitingForExchangeCountryInput = 'waiting_for_exchange_country_input';
    case WaitingForExchangePicked = 'waiting_for_exchange_picked';
}
