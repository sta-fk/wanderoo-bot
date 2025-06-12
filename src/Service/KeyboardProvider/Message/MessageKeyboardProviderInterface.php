<?php

namespace App\Service\KeyboardProvider\Message;

use App\DTO\Request\TelegramMessage;
use App\Service\KeyboardProvider\KeyboardProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('message_keyboard_provider')]
interface MessageKeyboardProviderInterface extends KeyboardProviderInterface
{
    public function supports(TelegramMessage $message): bool;
}
