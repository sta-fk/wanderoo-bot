<?php

namespace App\Service\TelegramViewer\PlanGenerationFinishedViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\PlanGenerationFinishedViewData\PlanSaveResultViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PlanSaveResultViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::PlanSaveResult);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof PlanSaveResultViewData);

        if (null !== $data->tripTitle) {
            return new AnswerCallbackQueryContext(
                callbackQueryId: $data->callbackQueryId,
                text: $this->translator->trans('trip.saved.success', ['{title}' => $data->tripTitle]),
            );
        }

        return new AnswerCallbackQueryContext(
            callbackQueryId: $data->callbackQueryId,
            text: $this->translator->trans('trip.saved.failed'),
        );
    }
}
