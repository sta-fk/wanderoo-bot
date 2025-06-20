<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CurrencyChoiceViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CurrencyChoiceViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::CurrencyChoice->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CurrencyChoiceViewData);

        $keyboard =  [
                [[
                    'text' => $this->translator->trans("trip.context.currency.choice.keyboard." . CallbackQueryData::Usd->value),
                    'callback_data' => CallbackQueryData::CurrencyChoice->value . CallbackQueryData::Usd->value
                ]],
                [[
                    'text' => $this->translator->trans("trip.context.currency.choice.keyboard." . CallbackQueryData::Eur->value),
                    'callback_data' => CallbackQueryData::CurrencyChoice->value . CallbackQueryData::Eur->value
                ]],
                [[
                    'text' => $this->translator->trans("trip.context.currency.choice.keyboard." . CallbackQueryData::FromCountry->value),
                    'callback_data' => CallbackQueryData::CurrencyChoice->value . CallbackQueryData::FromCountry->value
                ]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans("trip.context.currency.choice.message"),
            replyMarkup: ['inline_keyboard' => $keyboard]
        );
    }
}
