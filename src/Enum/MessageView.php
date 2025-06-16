<?php

namespace App\Enum;

enum MessageView: string
{
    case Start = 'start';
    case StartNew = 'start_new';
    case EnterCountryName = 'enter_country_name';
}
