<?php

namespace App\Service\TelegramViewer\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\BudgetViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\BudgetOptionsProvider;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class BudgetViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
        private BudgetOptionsProvider $budgetOptionsProvider,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::Budget->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof BudgetViewData);

        $keyboardItems = $this->budgetOptionsProvider->getBudgetOptionsInCurrency($data->currency);

        $budgetKeyboard = [];
        foreach ($keyboardItems as $callback => $label) {
            $budgetKeyboard[] = [[
                'text' => $label,
                'callback_data' => CallbackQueryData::Budget->value . $callback,
            ]];
        }

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.budget.message'),
            replyMarkup: ['inline_keyboard' => $budgetKeyboard],
        );
    }
}
