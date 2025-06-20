<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\StartDateViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\FlowStepService\Trait\BuildCalendarKeyboardTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class StartDateViewer implements TelegramViewerInterface
{
    use BuildCalendarKeyboardTrait;

    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::StartDate->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof StartDateViewData);

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $keyboard = $this->buildCalendarKeyboard($now->format('Y'), $now->format('m'));

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.start_date.message'),
            replyMarkup: $keyboard,
        );
    }
}
