<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ReuseOrNewTripStyleViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
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
            'inline_keyboard' => [[
                ['text' => '✅ Так', 'callback_data' => CallbackQueryData::TripStyle->value . CallbackQueryData::Reuse->value],
                ['text' => '❌ Ні', 'callback_data' => CallbackQueryData::TripStyle->value . CallbackQueryData::New->value],
            ]]
        ];

        return new SendMessageContext(
            $data->chatId,
            $this->translator->trans('trip.context.trip_style.reuse_or_new', ['{last_one_trip_style}' => $data->lastOneTripStyle]),
            ['inline_keyboard' => $keyboard]
        );
    }
}
