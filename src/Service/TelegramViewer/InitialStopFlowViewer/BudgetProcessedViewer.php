<?php

namespace App\Service\TelegramViewer\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\BudgetProcessedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class BudgetProcessedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::BudgetProcessed);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof BudgetProcessedViewData);

        $messageText = $this->translator->trans('trip.context.budget.processed', ['{budget}' => $data->budget, '{currency}' => $data->currency]);

        return new SendMessageContext(chatId: $data->chatId, text: $messageText);
    }
}
