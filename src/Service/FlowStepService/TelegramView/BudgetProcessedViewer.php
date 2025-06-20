<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\BudgetProcessedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class BudgetProcessedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::BudgetProcessed->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof BudgetProcessedViewData);

        return new SendMessageContext(
            $data->chatId,
            $this->translator->trans(
                'trip.context.budget.processed',
                ['{budget}' => $data->budget, '{currency}' => $data->currency],
            ),
        );
    }
}
