<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CountryPickedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;

final readonly class CountryPickedViewer implements TelegramViewerInterface
{
    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::CountryPicked->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CountryPickedViewData);

        return new AnswerCallbackQueryContext($data->callbackQueryId);
    }
}
