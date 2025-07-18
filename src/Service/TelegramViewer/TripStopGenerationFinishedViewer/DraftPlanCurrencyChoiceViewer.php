<?php

namespace App\Service\TelegramViewer\TripStopGenerationFinishedViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\DTO\Internal\TripStopGenerationFinishedViewData\DraftPlanCurrencyChoiceViewData;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DraftPlanCurrencyChoiceViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::ExchangeChoice);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DraftPlanCurrencyChoiceViewData);

        $keyboard = [
            [[
                'text' => $this->translator->trans("trip.context.exchange.choice.keyboard.usd"),
                'callback_data' => CallbackQueryData::DraftPlanCurrencyChoice->value . CallbackQueryData::Usd->value
            ]],
            [[
                'text' => $this->translator->trans("trip.context.exchange.choice.keyboard.eur"),
                'callback_data' => CallbackQueryData::DraftPlanCurrencyChoice->value . CallbackQueryData::Eur->value
            ]],
            [[
                'text' => $this->translator->trans("trip.context.exchange.choice.keyboard.auto"),
                'callback_data' => CallbackQueryData::DraftPlanCurrencyChoice->value . CallbackQueryData::Auto->value
            ]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans("trip.context.exchange.choice.message"),
            replyMarkup: ['inline_keyboard' => $keyboard]
        );
    }
}
