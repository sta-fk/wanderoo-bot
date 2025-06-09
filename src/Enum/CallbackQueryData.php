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
    case NewTrip = 'new_trip';

    // For Adding stop flow
    case StopCountrySame = 'stop_country_same';
    case StopCountryAnother = 'stop_country_another';

    // Query parts
    case Reuse = 'reuse';
    case New = 'new';
    case Custom = 'custom';
}
