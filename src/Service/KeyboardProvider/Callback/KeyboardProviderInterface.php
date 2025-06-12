<?php

namespace App\Service\KeyboardProvider\Callback;

use App\DTO\Request\TelegramUpdate;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

interface KeyboardProviderInterface
{
    public function getTextMessage(int $chatId = 0): string;
    public function buildKeyboard(int $chatId = 0): ?array;
}
