<?php

namespace App\Service\TelegramViewer\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\CalendarViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\EditMessageTextContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use App\Service\ViewDataBuilder\Trait\BuildCalendarKeyboardTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CalendarViewer implements TelegramViewerInterface
{
    use BuildCalendarKeyboardTrait;

    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::Calendar);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CalendarViewData);

        return new EditMessageTextContext(
            chatId: $data->chatId,
            messageId: $data->messageId,
            text: $this->translator->trans('trip.context.start_date.message'),
            replyMarkup: $this->buildCalendarKeyboard($data->year, $data->month),
        );
    }
}
