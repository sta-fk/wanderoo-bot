<?php

namespace App\Service\KeyboardProvider\Callback;

use App\DTO\Request\TelegramMessage;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('message_keyboard_provider')]
interface MessageKeyboardProviderInterface extends KeyboardProviderInterface
{
    public function supports(TelegramMessage $message): bool;
}
