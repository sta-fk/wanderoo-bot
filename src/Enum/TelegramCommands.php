<?php

namespace App\Enum;

enum TelegramCommands: string
{
    case Start = '/start';
    case NewTrip = '/new_trip';
    case AddStop = '/add_stop';
}
