<?php

namespace App\Service\TelegramViewer\MenuActionsViewer\ViewPlanDetailsActionsViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData\SavedPlanNotFoundViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class SavedPlanNotFoundViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::SavedPlanNotFound);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof SavedPlanNotFoundViewData);

        return new AnswerCallbackQueryContext(
            callbackQueryId: $data->callbackQueryId,
            text: $this->translator->trans("trip.list.item.not_found"),
        );
    }
}
