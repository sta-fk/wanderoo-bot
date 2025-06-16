<?php

namespace App\Service;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramResponseMessage\TelegramMessageInterface;

interface MessageFactoryInterface
{
    public function create(MessageViewIdentifier $identifier, ViewDataInterface $data): TelegramMessageInterface;
}
