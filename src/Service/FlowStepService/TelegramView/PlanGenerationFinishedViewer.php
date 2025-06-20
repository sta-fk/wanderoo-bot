<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\PlanGenerationFinishedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PlanGenerationFinishedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::PlanGenerationFinished->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof PlanGenerationFinishedViewData);

        $keyboard = [
            [['text' => $this->translator->trans('trip.plan_generated.save'), 'callback_data' => CallbackQueryData::SaveGeneratedPlan->value]],
            [['text' => $this->translator->trans('trip.plan_generated.edit'), 'callback_data' => CallbackQueryData::EditGeneratedPlan->value]],
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
