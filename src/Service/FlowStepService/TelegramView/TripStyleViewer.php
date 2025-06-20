<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\TripStyleViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class TripStyleViewer implements TelegramViewerInterface
{
    public const TRIP_STYLE_OPTIONS = [
        'light' => '🧘 Лайтовий',
        'active' => '🚀 Активний',
        'mixed' => '🎭 Змішаний',
        'relax' => '🛌 Релакс',
        'cultural' => '🏛️ Культурний',
        'roadtrip' => '🚗 Роадтрип',
        'luxury' => '💎 Люкс',
        'budget' => '💰 Бюджетний',
    ];

    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::TripStyle->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof TripStyleViewData);

        $keyboard = [];

        foreach (array_chunk(self::TRIP_STYLE_OPTIONS, 2, true) as $pair) {
            $row = [];

            foreach ($pair as $key => $label) {
                $row[] = ['text' => $label, 'callback_data' => CallbackQueryData::TripStyle->value . $key];
            }

            $keyboard[] = $row;
        }

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.trip_style.message'),
            replyMarkup: ['inline_keyboard' => $keyboard],
        );
    }
}
