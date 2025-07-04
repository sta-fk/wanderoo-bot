<?php

namespace App\Service\TelegramViewer\MenuActionsViewer\ViewPlanDetailsActionsViewer;

use App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData\ViewedPlanCurrencyChangedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ViewedPlanCurrencyPickedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::ViewedPlanCurrencyChanged);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ViewedPlanCurrencyChangedViewData);

        return new AnswerCallbackQueryContext(
            callbackQueryId: $data->callbackQueryId,
            text: $this->translator->trans("commands.view_saved.details.exchange_currency.success", [
                '{currency}' => $data->currency
            ]),
            showAlert: true
        );
    }
}
