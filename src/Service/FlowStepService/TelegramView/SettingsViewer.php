<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\AddStopViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\SettingsViewData;
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
        assert($data instanceof SettingsViewData);

        $keyboard = [
            [['text' => $this->translator->trans('menu.keyboard.settings.default_currency'), 'callback_data' => CallbackQueryData::DefaultCurrency->value]],
            [['text' => $this->translator->trans('menu.keyboard.settings.language'), 'callback_data' => CallbackQueryData::Language->value]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('menu.keyboard.settings.input'),
            replyMarkup: ['inline_keyboard' => $keyboard],
        );
    }
}
