<?php

namespace App\DTO\Internal;

use App\Enum\States;
use App\Enum\MessageView;

interface ViewDataInterface
{
    public function getCurrentView(): MessageView;
}
