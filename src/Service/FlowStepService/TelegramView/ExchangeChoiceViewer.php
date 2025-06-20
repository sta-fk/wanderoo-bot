<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\DTO\Internal\ExchangeChoiceViewData;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ExchangeChoiceViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::ExchangeChoice->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ExchangeChoiceViewData);

        $keyboard = [
            [[
                'text' => $this->translator->trans("trip.context.exchange.keyboard." . CallbackQueryData::Usd->value),
                'callback_data' => CallbackQueryData::ExchangeChoice->value . CallbackQueryData::Usd->value
            ]],
            [[
                'text' => $this->translator->trans("trip.context.exchange.keyboard." . CallbackQueryData::Eur->value),
                'callback_data' => CallbackQueryData::ExchangeChoice->value . CallbackQueryData::Eur->value
            ]],
            [[
                'text' => $this->translator->trans("trip.context.exchange.keyboard." . CallbackQueryData::FromCountry->value),
                'callback_data' => CallbackQueryData::ExchangeChoice->value . CallbackQueryData::FromCountry->value
            ]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans("trip.context.exchange.message"),
            replyMarkup: ['inline_keyboard' => $keyboard]
        );
    }
}
