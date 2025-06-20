<?php

namespace App\Service;

use App\DTO\Internal\ViewDataCollection;
use App\DTO\TelegramMessageResponse\TelegramMessageCollection;

interface MessageFactoryInterface
{
    public function create(ViewDataCollection $collection): TelegramMessageCollection;
}
