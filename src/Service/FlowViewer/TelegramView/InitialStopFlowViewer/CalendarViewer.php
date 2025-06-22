<?php

namespace App\Service\FlowViewer\TelegramView\InitialStopFlowViewer;

use App\DTO\Internal\CalendarViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\EditMessageTextContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\FlowViewer\TelegramView\TelegramViewerInterface;
use App\Service\FlowViewer\Trait\BuildCalendarKeyboardTrait;
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
        return MessageView::Calendar->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CalendarViewData);

        $keyboard = $this->buildCalendarKeyboard($data->year, $data->month);

        return new EditMessageTextContext(
            chatId: $data->chatId,
            messageId: $data->messageId,
            text: $this->translator->trans('trip.context.start_date.message'),
            replyMarkup: $keyboard,
        );
    }
}
