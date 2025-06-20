<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\AddStopViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class SettingsViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::Settings->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof AddStopViewData);

        $keyboard = [
            [['text' => $this->translator->trans('trip.menu.keyboard.settings.' . CallbackQueryData::Exchanger->value), 'callback_data' => CallbackQueryData::Exchanger->value]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.menu.keyboard.' . CallbackQueryData::Settings->value . '.input'),
            replyMarkup: ['inline_keyboard' => $keyboard],
        );
    }
}
