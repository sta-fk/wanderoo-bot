<?php

namespace App\DTO\Internal\ViewData;

use App\Enum\States;
use App\Enum\View;

interface ViewDataInterface
{
    public function getChatId(): int;
    public function getCurrentView(): View;
    public function getState(): States;
}
