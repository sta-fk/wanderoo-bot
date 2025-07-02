<?php

namespace App\Service\TelegramViewer\PlanGenerationFinishedViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\PlanGenerationFinishedViewData\EditStopDurationConfirmationViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;

readonly class EditStopDurationConfirmationViewer implements TelegramViewerInterface
{
    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::EditStopDurationConfirmation);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof EditStopDurationConfirmationViewData);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $data->message
        );
    }
}
