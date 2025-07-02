<?php

namespace App\Service\TelegramViewer\MenuActionsViewer\ViewPlanDetailsActionsViewer;

use App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData\ViewedPlanExchangerViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ViewedPlanExchangerViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::ViewedPlanExchanger);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ViewedPlanExchangerViewData);

        $lines = [
            $this->translator->trans("commands.view_saved.details.exchange_currency.budget", ['{amount}' => $data->baseAmount, '{currency}' => $data->baseCurrency]),
            "\n",
            $this->translator->trans("commands.view_saved.details.exchange_currency.converted"),
        ];

        $keyboard = [];
        $shortId = substr($data->tripId->toRfc4122(), 0, 8);

        foreach ($data->convertedAmounts as $currency => $amount) {
            $lines[] = sprintf("• %s %s", round($amount, -1), $currency);
            $keyboard[] = [[
                'text' => $this->translator->trans("commands.view_saved.details.exchange_currency.set_currency", ['{currency}' => $currency]),
                'callback_data' => CallbackQueryData::SetViewedPlanCurrency->value . $shortId . '_' . $currency,
            ]];
        }

//        $keyboard[] = [[
//            'text' => $this->translator->trans("commands.view_saved.details.exchange_currency.set_auto"),
//            'callback_data' => CallbackQueryData::SetViewedPlanCurrency->value . $shortId . '_' . CallbackQueryData::Auto->value,
//        ]];

        $text = implode("\n", $lines);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $text,
            replyMarkup: ['inline_keyboard' => $keyboard],
        );
    }
}
