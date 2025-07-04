<?php

namespace App\Service\TelegramViewer\MenuActionsViewer\SettingsActionsViewer;

use App\DTO\Internal\MenuActionsViewData\SettingsActionsViewData\DefaultCurrencyPickedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DefaultCurrencyPickedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::DefaultCurrencyPicked);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DefaultCurrencyPickedViewData);

        return new AnswerCallbackQueryContext(
            callbackQueryId: $data->callbackQueryId,
            text: $this->translator->trans('trip.default_currency.picked', [
                '{currency}' => $data->currency,
            ]),
            showAlert: true
        );
    }
}
