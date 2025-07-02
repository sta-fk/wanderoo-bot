<?php

namespace App\Enum;

enum MessageView: string
{
    case Menu = 'menu';
    case StartNew = 'start_new';
    case CountryInput = 'country_input';
    case CountryInputSearchResult = 'country_search';
    case CountryPicked = 'country_picked';
    case CityInput = 'city_input';
    case CityInputSearchResult = 'city_search';
    case CityPicked = 'city_picked';
    case Duration = 'duration';
    case CustomDurationInput = 'custom_duration_input';
    case DurationProcessed = 'custom_duration_processed';
    case StartDate = 'start_date';
    case Calendar = 'calendar';
    case DatePicked = 'date_picked';
    case TripStyle = 'trip_style';
    case TripStylePicked = 'trip_style_picked';
    case Interests = 'interests';
    case Budget = 'budget';
    case CustomBudgetInput = 'custom_budget';
    case BudgetProcessed = 'budget_processed';
    case TripStopCreationFinished = 'stop_creation_finished';
    case PlanIsGenerating = 'plan_is_generating';
    case PlanGenerationFinished = 'plan_generation_finished';
    case EditPlanContextEntry = 'edit_plan_context_entry';
    case EditPlanStop = 'edit_stop';
    case EditStopDurationRequest = 'edit_stop_duration_request';
    case EditStopDurationConfirmation = 'edit_stop_duration_confirmation';

    case AddStop = 'add_stop';
    case ReuseOrNewTripStyle = 'reuse_or_new_trip_style';
    case ReuseTripStyle = 'reuse_trip_style';
    case ReuseOrNewInterests = 'reuse_or_new_interests';
    case CurrencyChoice = 'currency_choice';
    case CurrencyCountryInput = 'currency_country_input';
    case CurrencyCountryInputSearchResult = 'currency_country_search';
    case CurrencyPicked = 'currency_picked';

    case PlanSaveResult = 'save_generated_plan';
    case ViewSavedPlansList = 'view_saved_plans_list';
    case SavedPlanNotFound = 'saved_plan_not_found';
    case PlanDetailsShown = 'saved_plan_regenerated';
    case ViewedPlanExchanger = 'viewed_plan_exchanger';
    case ViewedPlanCurrencyCountryInput = 'viewed_plan_currency_country_input';
    case ViewedPlanCurrencyCountryInputSearchResult = 'viewed_plan_currency_country_search';
    case ViewedPlanCurrencyChanged = 'viewed_plan_exchange_currency_changed';
    case DeletePlan = 'delete_plan';
    case Settings = 'settings';
    case DefaultCurrency = 'default_currency';
    case DefaultCurrencyPicked = 'default_currency_picked';
    case DefaultCurrencyCountryInput = 'default_currency_country_input';
    case DefaultCurrencyCountryInputSearchResult = 'default_currency_country_search';
    case ViewCurrentDraftPlan = 'view_current_draft_plan';

    case ExchangeChoice = 'exchange_choice';
    case ExchangeCountryInput = 'exchange_country_input';
    case ExchangeCountryInputSearchResult = 'exchange_country_input_search';
    case ExchangePicked = 'exchange_picked';

    case UniversalDeletePreviousMessage = 'universal_delete_previous_message';
}
