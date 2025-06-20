<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CityInputViewData;
use App\DTO\Internal\CustomDurationInputViewData;
use App\DTO\Internal\DurationProcessedViewData;
use App\DTO\Internal\PlanIsGeneratingViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

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
            $data->chatId,
            $data->tripPlanSplitMessage,
        );
    }
}
