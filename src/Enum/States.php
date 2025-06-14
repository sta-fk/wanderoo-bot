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
    case GeneratingTripPlan = 'generating_trip_plan';

    // Add stop flow
    case WaitingForStopCountry = 'waiting_for_stop_country';
    case WaitingForReuseOrNewTripStyle = 'waiting_for_reuse_or_new_trip_style';
    case WaitingForReuseOrNewInterests = 'waiting_for_reuse_or_new_interests';
    case WaitingForCurrencyChoice = 'waiting_for_currency_choice';
    case WaitingForCurrencyCountrySearch = 'waiting_for_currency_country_search';
    case WaitingForCurrencyCountryPick = 'waiting_for_currency_country_pick';

    // Currency exchanger command
    case WaitingForExchangeChoice = 'waiting_for_exchange_choice';
    case WaitingForExchangeCountrySearch = 'waiting_for_exchange_country_search';
    case WaitingForExchangeCountryPick = 'waiting_for_exchange_country_pick';
    case ExchangeDone = 'exchange_done';
}
