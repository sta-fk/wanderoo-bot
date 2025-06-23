<?php

namespace App\Service\FlowViewer\TelegramView\TripStopGenerationFinishedViewer;

use App\DTO\Internal\TripStopGenerationFinishedViewData\DraftPlanCurrencyPickedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\FlowViewer\TelegramView\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DraftPlanCurrencyPickedViewer implements TelegramViewerInterface
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
        assert($data instanceof DraftPlanCurrencyPickedViewData);

        $messageText = $this->translator->trans('trip.context.exchange.result', [
            '{toCurrency}' => $data->toCurrency,
            '{toAmount}' => $data->toAmount,
            '{fromCurrency}' => $data->fromCurrency,
            '{fromAmount}' => $data->fromAmount,
        ]);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $messageText,
        );
    }
}
