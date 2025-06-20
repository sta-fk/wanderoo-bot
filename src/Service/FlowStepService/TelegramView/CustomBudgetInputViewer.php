<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CityInputViewData;
use App\DTO\Internal\CustomBudgetInputViewData;
use App\DTO\Internal\CustomDurationInputViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
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
                $data->chatId,
                $this->translator->trans('trip.context.custom_budget.validation_failed'),
            );
        }

        return new SendMessageContext(
            $data->chatId,
            $this->translator->trans('trip.context.custom_budget.input', ['{currency}' => $data->currency, '{potential_amount}' => $data->potentialAmount]),
        );
    }
}
