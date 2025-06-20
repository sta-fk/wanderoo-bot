<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\PlanIsGeneratingViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;

final readonly class PlanIsGeneratingViewer implements TelegramViewerInterface
{
    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::PlanIsGenerating->value === $identifier->value;
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
