<?php

namespace App\Service\FlowViewer\TelegramView\MenuActionsViewer\SettingsActionsViewer;

use App\DTO\Internal\DefaultCurrencyMenuViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\FlowViewer\TelegramView\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DefaultCurrencyMenuViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::DefaultCurrency->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DefaultCurrencyMenuViewData);

        $keyboard = [
            [[
                'text' => $this->translator->trans("trip.default_currency.choice.keyboard.usd"),
                'callback_data' => CallbackQueryData::DefaultCurrencyChoice->value . CallbackQueryData::Usd->value
            ]],
            [[
                'text' => $this->translator->trans("trip.default_currency.choice.keyboard.eur"),
                'callback_data' => CallbackQueryData::DefaultCurrencyChoice->value . CallbackQueryData::Eur->value
            ]],
            [[
                'text' => $this->translator->trans("trip.default_currency.choice.keyboard.auto"),
                'callback_data' => CallbackQueryData::DefaultCurrencyChoice->value . CallbackQueryData::Auto->value
            ]],
            [[
                'text' => $this->translator->trans("trip.default_currency.choice.keyboard.back"),
                'callback_data' => CallbackQueryData::BackToMenu->value
            ]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans("trip.default_currency.choice.message"),
            replyMarkup: [
                'inline_keyboard' => $keyboard
            ]
        );
    }
}
