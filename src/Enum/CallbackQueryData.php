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
    case AddStop = 'add_stop';
    case ConfirmStop = 'confirm_stop';
    case ReadyToBuildPlan = 'ready_to_build_plan';

    // For Adding stop flow
    case StopCountry = 'stop_country_';
    case StopCountrySame = 'stop_country_same';
    case StopCountryAnother = 'stop_country_another';
    case StopDuration = 'stop_duration_';
    case StopTripStyle = 'stop_trip_style_';
    case StopInterest = 'stop_interest_';
    case StopInterestsDone = 'stop_interest_done';
    case StopBudget = 'stop_budget_';

    // Query parts
    case Reuse = 'reuse';
    case New = 'new';
    case Custom = 'custom';
}
