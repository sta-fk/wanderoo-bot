<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramResponseMessage\TelegramMessageInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('telegram.view')]
interface TelegramViewInterface
{
    public function supports(MessageViewIdentifier $identifier): bool;

    public function render(ViewDataInterface $data): TelegramMessageInterface;
}
