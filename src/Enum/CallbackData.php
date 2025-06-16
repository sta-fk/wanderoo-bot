<?php

namespace App\Enum;

enum CallbackData: string
{
    case StartNew = 'start_new';
    case ViewSaved = 'view_saved';
}
