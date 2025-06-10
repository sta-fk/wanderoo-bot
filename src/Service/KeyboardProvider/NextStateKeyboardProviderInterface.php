<?php

namespace App\Service\KeyboardProvider;

use App\Enum\States;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('flow_step_keyboard_provider')]
interface NextStateKeyboardProviderInterface
{
    public function supports(States $requiredState): bool;
    public function getTextMessage(int $chatId = 0): string;
    public function buildKeyboard(array $keyboardItems = []): ?array;
}
