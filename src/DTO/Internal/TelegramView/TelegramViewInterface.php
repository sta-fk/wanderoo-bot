<?php

namespace App\DTO\Internal\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewData\ViewDataInterface;
use App\DTO\TelegramMessage\TelegramMessageInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('telegram.view')]
interface TelegramViewInterface
{
    public function supports(MessageViewIdentifier $identifier): bool;

    public function render(ViewDataInterface $data): TelegramMessageInterface;
}
