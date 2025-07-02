<?php

namespace App\Service\TelegramViewer\MenuActionsViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\MenuActionsViewData\StartNewViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class StartNewViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::StartNew);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof StartNewViewData);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.start_new.prompt'),
        );
    }
}
