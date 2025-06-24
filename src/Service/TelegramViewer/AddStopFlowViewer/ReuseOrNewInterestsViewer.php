<?php

namespace App\Service\TelegramViewer\AddStopFlowViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\AddStopFlowViewData\ReuseOrNewInterestsViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ReuseOrNewInterestsViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::ReuseOrNewInterests->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ReuseOrNewInterestsViewData);

        $keyboard = [
            [
                ['text' => $this->translator->trans('trip.default_keyboard.yes'), 'callback_data' => CallbackQueryData::Interest->value . CallbackQueryData::Reuse->value],
                ['text' => $this->translator->trans('trip.default_keyboard.no'), 'callback_data' => CallbackQueryData::Interest->value . CallbackQueryData::New->value],
            ]
        ];

        $text = $this->translator->trans('trip.context.interests.reuse_or_new');
        if (!empty($data->interests)) {
            $text .= "\n" . implode(', ', $data->interests);
        }

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $text,
            replyMarkup: ['inline_keyboard' => $keyboard],
        );
    }
}
