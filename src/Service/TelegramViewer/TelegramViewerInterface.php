<?php

namespace App\Service\TelegramViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('telegram_viewer')]
interface TelegramViewerInterface
{
    public function supports(MessageViewIdentifier $identifier): bool;

    public function render(ViewDataInterface $data): TelegramMessageInterface;
}
