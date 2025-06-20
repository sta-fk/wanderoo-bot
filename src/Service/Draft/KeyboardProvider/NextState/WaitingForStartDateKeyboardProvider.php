<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\DTO\Context\PlanContext;
use App\Enum\States;
use App\Service\Draft\KeyboardProvider\NextState\BuildCalendarKeyboardTrait;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;
use App\Service\UserStateStorage;

readonly class WaitingForStartDateKeyboardProvider implements NextStateKeyboardProviderInterface
{
    use BuildCalendarKeyboardTrait;

    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::WaitingForStartDate === $requiredState;
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return $this->buildCalendarKeyboard($now->format('Y'), $now->format('m'));
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (0 === $chatId) {
            throw new \LogicException("Keyboard is not configured");
        }

        $context = $this->userStateStorage->getContext($chatId);

        return "Чудово! Подорож на {$context->currentStopDraft->duration} днів. \n\n📅 Тепер оберіть дату подорожі:";
    }
}
