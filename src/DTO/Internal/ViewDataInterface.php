<?php

namespace App\DTO\Internal;

use App\Enum\States;
use App\Enum\MessageView;

interface ViewDataInterface
{
    public function getChatId(): int;
    public function getCurrentView(): MessageView;
    public function getNextStates(): States;
}
