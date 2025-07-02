<?php

namespace App\Service\TelegramViewer\TripStopGenerationFinishedViewer;

use App\DTO\Internal\TripStopGenerationFinishedViewData\PlanGenerationFinishedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PlanGenerationFinishedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::PlanGenerationFinished);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof PlanGenerationFinishedViewData);

        $keyboard = [
            [['text' => $this->translator->trans('trip.plan_generated.save'), 'callback_data' => CallbackQueryData::SaveGeneratedPlan->value]],
            [['text' => $this->translator->trans('trip.plan_generated.edit'), 'callback_data' => CallbackQueryData::EditPlan->value]],
            [['text' => $this->translator->trans('trip.plan_generated.start_over'), 'callback_data' => CallbackQueryData::StartNew->value]],
            [['text' => $this->translator->trans('trip.plan_generated.back'), 'callback_data' => CallbackQueryData::BackToMenu->value]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.plan_generated.message'),
            replyMarkup: ['inline_keyboard' => $keyboard]
        );
    }
}
