<?php

namespace App\Service\FlowViewer\TelegramView\InitialStopFlowViewer;

use App\DTO\Internal\DurationViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\FlowViewer\TelegramView\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DurationViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::Duration->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DurationViewData);

        $keyboard = [
            'inline_keyboard' => [
                [['text' => $this->translator->trans('trip.context.duration.keyboard.1_day'), 'callback_data' => CallbackQueryData::Duration->value . '1']],
                [['text' => $this->translator->trans('trip.context.duration.keyboard.3_day'), 'callback_data' => CallbackQueryData::Duration->value . '3']],
                [['text' => $this->translator->trans('trip.context.duration.keyboard.5_day'), 'callback_data' => CallbackQueryData::Duration->value . '5']],
                [['text' => $this->translator->trans('trip.context.duration.keyboard.7_day'), 'callback_data' => CallbackQueryData::Duration->value . '7']],
                [['text' => $this->translator->trans('trip.context.duration.keyboard.custom'), 'callback_data' => CallbackQueryData::Duration->value . CallbackQueryData::Custom->value]],
            ]
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.duration.message'),
            replyMarkup: $keyboard,
        );
    }
}
