<?php

namespace App\Enum;

use App\DTO\CallbackQueryDataAction;

enum CallbackQueryData: string
{
    case Country = 'country_';
    case City = 'city_';
    case Duration = 'duration_';
    case Calendar = 'calendar_';
    case DatePicked = 'date_picked_';
    case TripStyle = 'trip_style_';
    case Interest = 'interest_';
    case InterestsDone = 'interest_done';
    case Budget = 'budget_';
    case GeneratingTripPlan = 'generating_trip_plan';

    case ViewPlanDetails = 'view_plan_';
    case DeletePlan = 'delete_trip_';
    case ViewedPlanExchanger = 'exchanger_of_plan_';
    case SetViewedPlanCurrency = 'set_viewed_plan_exchange_currency_';

    // Editing
    case EditPlan = 'edit_plan';
    case EditPlanStop = 'edit_plan_stop_';
    case EditStopDuration = 'edit_stop_duration_';

    // Commands as callback
    case StartNew = 'start_new';
    case ViewSavedPlansList = 'view_saved_plans_list';
    case Settings = 'settings';
    case DraftPlanCurrency = 'exchanger';
    case DefaultCurrency = 'default_currency';
    case DefaultCurrencyChoice = 'default_currency_choice_';
    case DefaultCurrencyCountryPick = 'default_currency_country_pick_';
    case Language = 'language';
    case AddStop = 'add_stop';
    case SaveGeneratedPlan = 'save_generated_plan';
    case EditGeneratedPlan = 'edit_generated_plan';
    case BackToMenu = 'back_to_main_menu';

    // Currency exchanger command
    case DraftPlanCurrencyChoice = 'exchange_choice_';
    case DraftPlanCurrencyCountryPick = 'exchange_country_pick_';

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
    case Auto = 'from_country';

    public function withValue(string $value): string
    {
        if (str_ends_with($this->value, '_')) {
            return $this->value . $value;
        }

        return $this->value;
    }

    public function parseValue(string $data): string
    {
        return str_replace($this->value, '', $data);
    }
}
