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
}
