<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\AddStopViewData;
use App\DTO\Internal\DeletePlanViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DeletePlanViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::DeletePlan->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DeletePlanViewData);

        return new AnswerCallbackQueryContext(
            $data->callbackQueryId,
            $this->translator->trans("trip.deleted.success"),
            true
        );
    }
}
