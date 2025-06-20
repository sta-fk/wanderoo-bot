<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\ExchangePickedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ExchangePickedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::ExchangePicked->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ExchangePickedViewData);

        return new AnswerCallbackQueryContext(
            $data->callbackQueryId,
            $this->translator->trans('trip.context.exchange.result', [
                '{to_currency}' => $data->toCurrency,
                '{to_amount}' => $data->toAmount,
                '{from_currency}' => $data->fromCurrency,
                '{from_amount}' => $data->fromAmount,
            ]),
            true
        );
    }
}
