<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\ExchangeCountryInputViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ExchangeCountryInputViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::ExchangeCountryInput->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ExchangeCountryInputViewData);

        return new SendMessageContext(
            $data->chatId,
            $this->translator->trans("trip.context.exchange.country.input"),
        );
    }
}
