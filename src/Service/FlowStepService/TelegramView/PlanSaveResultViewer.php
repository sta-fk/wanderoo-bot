<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CityInputViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\PlanSaveResultViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PlanSaveResultViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::PlanSaveResult->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof PlanSaveResultViewData);

        if (null !== $data->tripTitle) {
            return new AnswerCallbackQueryContext(
                $data->callbackQueryId,
                $this->translator->trans('trip.context.save.success', ['{title}' => $data->tripTitle]),
                true
            );
        }

        return new AnswerCallbackQueryContext(
            $data->callbackQueryId,
            $this->translator->trans('trip.context.save.failed'),
            true
        );
    }
}
