<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ReuseTripStyleViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ReuseTripStyleViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::ReuseTripStyle->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ReuseTripStyleViewData);

        $messageText = $this->translator->trans(
            'trip.context.trip_style.reuse',
            ['{cityName}' => $data->cityName, '{tripStyle}' => $data->tripStyle]
        );

        return new AnswerCallbackQueryContext(
            callbackQueryId: $data->callbackQueryId,
            text: $messageText,
        );
    }
}
