<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\AddStopViewData;
use App\DTO\Internal\DefaultCurrencyViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DefaultCurrencyViewer implements TelegramViewerInterface
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
        assert($data instanceof DefaultCurrencyViewData);

        $keyboard = [
            [[
                'text' => $this->translator->trans("trip.default_currency.choice.keyboard.usd"),
                'callback_data' => 'currency.usd'
            ]],
            [[
                'text' => $this->translator->trans("trip.default_currency.choice.keyboard.eur"),
                'callback_data' => 'currency.eur'
            ]],
            [[
                'text' => $this->translator->trans("trip.default_currency.choice.keyboard.auto"),
                'callback_data' => 'currency.auto'
            ]],
            [[
                'text' => $this->translator->trans("trip.default_currency.choice.keyboard.back"),
                'callback_data' => 'settings.back'
            ]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('settings.currency.pick'),
            replyMarkup: [
                'inline_keyboard' => $keyboard
            ]
        );
    }
}
