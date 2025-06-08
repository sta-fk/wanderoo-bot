<?php

namespace App\Enum;

enum CallbackQueryData: string
{
    case StartYes = 'start_yes';
    case StartNo = 'start_no';
    case Country = 'country_';
    case CountryPage = 'country_page_';
    case City = 'city_';
    case CityPage = 'city_page_';
    case Duration = 'duration_';
    case Calendar = 'calendar_';
    case PickDate = 'pick_date_';
    case TripStyle = 'trip_style_';
    case Interest = 'interest_';
    case InterestsDone = 'interest_done';
    case Budget = 'budget_';

    case StopCountry = 'stop_country_';
    case StopCountryPage = 'stop_country_page_';
    case StopCity = 'stop_city_';
    case StopCityPage = 'stop_city_page_';
    case StopDuration = 'stop_duration_';
    case StopTripStyle = 'stop_trip_style_';
    case StopInterest = 'stop_interest_';
    case StopInterestsDone = 'stop_interest_done';
    case StopBudget = 'stop_budget_';

    case AddStop = 'add_stop';
    case ConfirmStop = 'confirm_stop';
    case ReadyToBuildPlan = 'ready_to_build_plan';
}
