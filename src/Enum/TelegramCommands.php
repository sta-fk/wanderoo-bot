<?php

namespace App\Enum;

enum TelegramCommands: string
{
    case Start = '/start';
    case StartNew = '/start_new';
    case ViewSavedPlansList = '/view_saved_plans_list';
    case Settings = '/settings';
    case ViewCurrentDraftPlan = '/view_current_draft_plan';
    case Exchanger = '/currency';
    case AddStop = '/add_stop';
}
