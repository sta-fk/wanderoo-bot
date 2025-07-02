<?php

namespace App\Service\TelegramViewer\TripStopGenerationFinishedViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\TripStopGenerationFinishedViewData\TripStopCreationFinishedViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class TripStopCreationFinishedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::TripStopCreationFinished);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof TripStopCreationFinishedViewData);

        $keyboard = [
            [[
                'text' => $this->translator->trans('trip.context.finished.keyboard.add_stop'),
                'callback_data' => CallbackQueryData::AddStop->value
            ]],
            [[
                'text' => $this->translator->trans('trip.context.finished.keyboard.generate_plan'),
                'callback_data' => CallbackQueryData::GeneratingTripPlan->value
            ]],
            [[
                'text' => $this->translator->trans('trip.context.finished.keyboard.exchange'),
                'callback_data' => CallbackQueryData::DraftPlanCurrency->value
            ]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.finished.message'),
            replyMarkup: ['inline_keyboard' => $keyboard],
        );
    }
}
