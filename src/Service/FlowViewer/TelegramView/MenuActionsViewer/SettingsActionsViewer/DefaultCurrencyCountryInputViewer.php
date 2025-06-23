<?php

namespace App\Service\FlowViewer\TelegramView\MenuActionsViewer\SettingsActionsViewer;

use App\DTO\Internal\MenuActionsViewData\SettingsActionsViewData\DefaultCurrencyCountryInputViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\FlowViewer\TelegramView\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DefaultCurrencyCountryInputViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::DefaultCurrencyCountryInput->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DefaultCurrencyCountryInputViewData);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.default_currency.country.input'),
        );
    }
}
