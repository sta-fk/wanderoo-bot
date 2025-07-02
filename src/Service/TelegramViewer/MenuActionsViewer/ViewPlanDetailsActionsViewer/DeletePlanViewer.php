<?php

namespace App\Service\TelegramViewer\MenuActionsViewer\ViewPlanDetailsActionsViewer;

use App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData\DeletePlanViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DeletePlanViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::DeletePlan);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DeletePlanViewData);

        return new AnswerCallbackQueryContext(
            callbackQueryId: $data->callbackQueryId,
            text: $this->translator->trans("trip.deleted.success"),
        );
    }
}
