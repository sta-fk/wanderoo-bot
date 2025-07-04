<?php

namespace App\Service\TelegramViewer\TripStopGenerationFinishedViewer;

use App\DTO\Internal\TripStopGenerationFinishedViewData\PlanIsGeneratingViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;

final readonly class PlanIsGeneratingViewer implements TelegramViewerInterface
{
    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::PlanIsGenerating);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof PlanIsGeneratingViewData);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $data->tripPlanSplitMessage,
        );
    }
}
