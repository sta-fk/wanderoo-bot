<?php

namespace App\Service\FlowViewer\TelegramView\AddStopFlowViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ReuseOrNewTripStyleViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\FlowViewer\TelegramView\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ReuseOrNewTripStyleViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::ReuseOrNewTripStyle->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ReuseOrNewTripStyleViewData);

        $keyboard = [
            [
                ['text' => $this->translator->trans('trip.default_keyboard.yes'), 'callback_data' => CallbackQueryData::TripStyle->value . CallbackQueryData::Reuse->value],
                ['text' => $this->translator->trans('trip.default_keyboard.no'), 'callback_data' => CallbackQueryData::TripStyle->value . CallbackQueryData::New->value],
            ]
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.trip_style.reuse_or_new', ['{lastOneTripStyle}' => $data->lastOneTripStyle]),
            replyMarkup: ['inline_keyboard' => $keyboard]
        );
    }
}
