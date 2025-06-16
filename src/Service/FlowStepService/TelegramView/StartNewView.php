<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\StartNewViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramResponseMessage\AnswerCallbackQueryContext;
use App\DTO\TelegramResponseMessage\SendMessageContext;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class StartNewView implements TelegramViewInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::StartNew->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): AnswerCallbackQueryContext
    {
        assert($data instanceof StartNewViewData);

        return new AnswerCallbackQueryContext(
            callbackQueryId: $data->callbackQueryId,
            text: $this->translator->trans('trip.context.start_new.message'),
            showAlert: true,
        );
    }
}
