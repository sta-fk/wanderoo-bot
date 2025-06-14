<?php

namespace App\Enum;

enum CallbackQueryData: string
{
    case StartYes = 'start_yes';
    case StartNo = 'start_no';
    case Country = 'country_';
    case City = 'city_';
    case Duration = 'duration_';
    case Calendar = 'calendar_';
    case PickDate = 'pick_date_';
    case TripStyle = 'trip_style_';
    case Interest = 'interest_';
    case InterestsDone = 'interest_done';
    case Budget = 'budget_';
    case ReadyToBuildPlan = 'ready_to_build_plan';
    case GeneratingTripPlan = 'generating_trip_plan';

    // Commands as callback
    case AddStop = 'add_stop';
    case NewTrip = 'new_trip';

    // Currency exchanger command
    case ExchangeChoice = 'exchange_choice_';
    case ExchangeCountryPick = 'exchange_country_pick_';

    // For Adding stop flow
    case StopCountrySame = 'stop_country_same';
    case StopCountryAnother = 'stop_country_another';
    case CurrencyChoice = 'currency_choice_';
    case CurrencyCountryPick = 'currency_country_pick_';

    // Query parts
    case Reuse = 'reuse';
    case New = 'new';
    case Custom = 'custom';
    case Usd = 'USD';
    case Eur = 'EUR';
    case FromCountry = 'from_country';
}
