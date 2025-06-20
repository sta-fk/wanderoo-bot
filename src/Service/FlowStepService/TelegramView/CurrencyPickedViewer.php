<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CityPickedViewData;
use App\DTO\Internal\CurrencyPickedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CurrencyPickedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::CurrencyPicked->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CurrencyPickedViewData);

        return new AnswerCallbackQueryContext(
            $data->callbackQueryId,
            $this->translator->trans('trip.context.currency.picked', [
                '{currency}' => $data->currency,
            ])
        );
    }
}
