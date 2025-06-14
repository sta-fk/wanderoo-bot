<?php

namespace App\Enum;

enum TelegramCommands: string
{
    case Start = '/start';
    case NewTrip = '/new_trip';
    case AddStop = '/add_stop';
    case ViewTrip = '/view_trip';
    case Exchanger = '/currency';
    case ViewGeneratedPlan = '/view_generated_plan';
}
