<?php

namespace App\Service\Draft\KeyboardProvider\NextState;

use App\Enum\CallbackQueryData;
use App\Enum\States;
use App\Service\Draft\FlowStepService\StartFlowStepService\TripStyleService;
use App\Service\Draft\KeyboardProvider\NextState\NextStateKeyboardProviderInterface;
use App\Service\UserStateStorage;

readonly class WaitingForTripStyleKeyboardProvider implements NextStateKeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function supports(States $requiredState): bool
    {
        return States::WaitingForTripStyle === $requiredState;
    }

    public function getTextMessage(int $chatId = 0): string
    {
        if (0 === $chatId) {
            throw new \LogicException("Keyboard is not configured");
        }

        $context = $this->userStateStorage->getContext($chatId);
        if ($context->isAddingStopFlow) {
            return "Який стиль подорожі ви бажаєте? 🧳";
        }

        return "✅ Подорож з <b>{$context->startDate->format('Y-m-d')}</b> по <b>{$context->endDate->format('Y-m-d')}</b> \n\nЯкий стиль подорожі ви бажаєте? 🧳";
    }

    public function buildKeyboard(array $keyboardItems = []): ?array
    {
        $keyboard = [];

        foreach (array_chunk(TripStyleService::TRIP_STYLE_OPTIONS, 2, true) as $pair) {
            $row = [];

            foreach ($pair as $key => $label) {
                $row[] = [
                    'text' => $label,
                    'callback_data' => CallbackQueryData::TripStyle->value . $key,
                ];
            }

            $keyboard[] = $row;
        }

        return [
            'inline_keyboard' => $keyboard,
        ];
    }
}
