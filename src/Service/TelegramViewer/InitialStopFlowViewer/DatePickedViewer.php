<?php

namespace App\Service\TelegramViewer\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\DatePickedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DatePickedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::DatePicked);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DatePickedViewData);

        return new AnswerCallbackQueryContext(
            callbackQueryId: $data->callbackQueryId,
            text: $this->translator->trans('trip.context.date_picked.message', [
                '{startDate}' => $data->startDate->format('Y-m-d'),
                '{endDate}' => $data->endDate->format('Y-m-d'),
            ])
        );
    }
}
