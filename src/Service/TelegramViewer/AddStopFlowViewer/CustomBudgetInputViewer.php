<?php

namespace App\Service\TelegramViewer\AddStopFlowViewer;

use App\DTO\Internal\AddStopFlowViewData\CustomBudgetInputViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CustomBudgetInputViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::CustomBudgetInput->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CustomBudgetInputViewData);

        if (!$data->validationPassed) {
            return new SendMessageContext(
                chatId: $data->chatId,
                text: $this->translator->trans('trip.context.custom_budget.validation_failed'),
            );
        }

        $messageText = $this->translator->trans(
            'trip.context.custom_budget.input',
            ['{currency}' => $data->currency, '{potentialAmount}' => $data->potentialAmount]
        );

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $messageText,
        );
    }
}
