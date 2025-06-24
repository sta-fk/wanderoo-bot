<?php

namespace App\Service\TelegramViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\UniversalDeletePreviousMessageViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\DeleteMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;

final readonly class UniversalDeletePreviousMessageViewer implements TelegramViewerInterface
{
    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::UniversalDeletePreviousMessage->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof UniversalDeletePreviousMessageViewData);

        return new DeleteMessageContext(
            chatId: $data->chatId,
            messageId: $data->messageIdToDelete,
        );
    }
}
