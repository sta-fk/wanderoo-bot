<?php

namespace App\Service;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewData\ViewDataInterface;
use App\DTO\TelegramMessage\TelegramMessageInterface;

interface MessageFactoryInterface
{
    public function create(MessageViewIdentifier $identifier, ViewDataInterface $data): TelegramMessageInterface;
}
