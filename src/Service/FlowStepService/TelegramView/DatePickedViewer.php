<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CityPickedViewData;
use App\DTO\Internal\DatePickedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DatePickedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::DatePicked->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DatePickedViewData);

        return new AnswerCallbackQueryContext(
            $data->callbackQueryId,
            $this->translator->trans('trip.context.date_picked.message', [
                '{start_date}' => $data->startDate->format('Y-m-d'),
                '{end_date}' => $data->endDate->format('Y-m-d'),
            ])
        );
    }
}
