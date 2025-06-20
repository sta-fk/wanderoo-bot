<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CityPickedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;

final readonly class CityPickedViewer implements TelegramViewerInterface
{
    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::CityPicked->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CityPickedViewData);

        return new AnswerCallbackQueryContext($data->callbackQueryId);
    }
}
