<?php

namespace App\Service\KeyboardProvider\Callback;

use App\DTO\Request\TelegramCallbackQuery;
use App\Service\KeyboardProvider\KeyboardProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('callback_keyboard_provider')]
interface CallbackKeyboardProviderInterface extends KeyboardProviderInterface
{
    public function supports(TelegramCallbackQuery $callbackQuery): bool;
}
