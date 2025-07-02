<?php

namespace App\Service\TelegramViewer\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\DurationViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DurationViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::Duration);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DurationViewData);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.duration.prompt'),
            replyMarkup: $this->getKeyboard(),
        );
    }

    private function getKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [['text' => $this->translator->trans('trip.context.duration.keyboard.1_day'), 'callback_data' => CallbackQueryData::Duration->withValue('1')]],
                [['text' => $this->translator->trans('trip.context.duration.keyboard.3_day'), 'callback_data' => CallbackQueryData::Duration->withValue('3')]],
                [['text' => $this->translator->trans('trip.context.duration.keyboard.5_day'), 'callback_data' => CallbackQueryData::Duration->withValue('5')]],
                [['text' => $this->translator->trans('trip.context.duration.keyboard.7_day'), 'callback_data' => CallbackQueryData::Duration->withValue('7')]],
                [['text' => $this->translator->trans('trip.context.duration.keyboard.custom'), 'callback_data' => CallbackQueryData::Duration->withValue(CallbackQueryData::Custom->value)]],
            ]
        ];
    }
}
