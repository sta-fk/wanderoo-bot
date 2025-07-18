<?php

namespace App\Service\TelegramViewer\TripStopGenerationFinishedViewer;

use App\DTO\Internal\TripStopGenerationFinishedViewData\AddStopViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class AddStopViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::AddStop);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof AddStopViewData);

        $keyboard = [
            [['text' => $this->translator->trans('trip.context.add_stop.keyboard.yes'), 'callback_data' => CallbackQueryData::StopCountryAnother->value]],
            [[
                'text' => $this->translator->trans('trip.context.add_stop.keyboard.no', ['{lastOneCountryName}' => $data->lastOneCountryName]),
                'callback_data' => CallbackQueryData::StopCountrySame->value
            ]],
        ];

        $messageText = $this->translator->trans("trip.context.add_stop.message", ['{lastOneCountryName}' => $data->lastOneCountryName]);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $messageText,
            replyMarkup: ['inline_keyboard' => $keyboard]
        );
    }
}
