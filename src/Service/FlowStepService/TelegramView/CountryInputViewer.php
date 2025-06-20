<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CountryInputViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CountryInputViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::CountryInput->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CountryInputViewData);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.country.input'),
        );
    }
}
