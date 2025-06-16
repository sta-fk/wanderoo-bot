<?php

namespace App\Service\Draft\KeyboardProvider;

use App\Enum\CallbackQueryData;
use App\Service\Draft\KeyboardProvider\KeyboardProviderInterface;
use App\Service\UserStateStorage;

readonly class AddStopKeyboardProvider implements KeyboardProviderInterface
{
    public function __construct(
        private UserStateStorage $userStateStorage,
    ) {
    }

    public function getTextMessage(int $chatId = 0): string
    {
        $context = $this->userStateStorage->getContext($chatId);

        $lastOneCountryName = null;
        if (null !== $context->currentStopDraft) {
            $lastOneCountryName = $context->currentStopDraft->countryName;
        } elseif (!empty($context->stops)) {
            $lastOneCountryName = ($context->stops[count($context->stops) - 1])->countryName;
        }

        return null !== $lastOneCountryName
            ? "Поточна країна в цій подорожі: {$lastOneCountryName}. Бажаєте відвідати ще одну країну?"
            : "Чи бажаєте ще в іншу країну?";
    }

    public function buildKeyboard(int $chatId = 0): ?array
    {
        $context = $this->userStateStorage->getContext($chatId);

        $negativeTextWithLastCountry = "❌ Ні, продовжу подорож в поточній країні";
        if (null !== $context->currentStopDraft?->countryName) {
            $lastOneCountryName = $context->currentStopDraft->countryName;
            $negativeTextWithLastCountry = "❌ Ні, продовжу подорож в {$lastOneCountryName}";
        } elseif (!empty($context->stops)) {
            $lastOneCountryName = ($context->stops[count($context->stops) - 1])->countryName;
            $negativeTextWithLastCountry = "❌ Ні, продовжу подорож в {$lastOneCountryName}";
        }

        return [
            'inline_keyboard' => [
                [
                    ['text' => "✅ Хочу ще в іншу країну", 'callback_data' => CallbackQueryData::StopCountryAnother->value],
                ],
                [
                    ['text' => $negativeTextWithLastCountry, 'callback_data' => CallbackQueryData::StopCountrySame->value],
                ],
            ],
        ];
    }
}
