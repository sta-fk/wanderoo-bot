<?php

namespace App\Service\TelegramViewer\PlanGenerationFinishedViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\PlanGenerationFinishedViewData\EditTripStopViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EditTripStopViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::EditPlanContextEntry->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof EditTripStopViewData);

        $text = $this->translator->trans('trip.edit.stop.header', [
            '{city}' => $data->cityName,
            '{country}' => $data->countryName,
        ]);

        $stopIndex = $data->stopIndex;
        $keyboard = [
            [[
                'text' => $this->translator->trans('trip.edit.option.style'),
                'callback_data' => "edit_trip_stop_style_{$stopIndex}",
            ]],
            [[
                'text' => $this->translator->trans('trip.edit.option.interests'),
                'callback_data' => "edit_trip_stop_interests_{$stopIndex}",
            ]],
            [[
                'text' => $this->translator->trans('trip.edit.option.duration'),
                'callback_data' => "edit_trip_stop_duration_{$stopIndex}",
            ]],
            [[
                'text' => $this->translator->trans('trip.edit.option.budget'),
                'callback_data' => "edit_trip_stop_budget_{$stopIndex}",
            ]],
            [[
                'text' => $this->translator->trans('common.delete'),
                'callback_data' => "edit_trip_stop_delete_{$stopIndex}",
            ]],
            [[
                'text' => $this->translator->trans('common.back'),
                'callback_data' => "edit_plan_back_to_stops",
            ]],
        ];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $text,
            replyMarkup: ['inline_keyboard' => $keyboard],
        );
    }
}
