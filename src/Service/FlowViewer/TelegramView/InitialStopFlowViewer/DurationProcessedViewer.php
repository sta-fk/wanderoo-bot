<?php

namespace App\Service\FlowViewer\TelegramView\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\DurationProcessedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\FlowViewer\TelegramView\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DurationProcessedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::DurationProcessed->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DurationProcessedViewData);

        $messageText = $this->translator->trans('trip.context.duration.processed', ['{currentStopDuration}' => $data->duration]);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $messageText,
        );
    }
}
