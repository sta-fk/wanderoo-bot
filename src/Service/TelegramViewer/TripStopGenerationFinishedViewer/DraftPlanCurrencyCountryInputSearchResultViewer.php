<?php

namespace App\Service\TelegramViewer\TripStopGenerationFinishedViewer;

use App\DTO\Internal\TripStopGenerationFinishedViewData\DraftPlanCurrencyCountryInputSearchResultViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DraftPlanCurrencyCountryInputSearchResultViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::ExchangeCountryInputSearchResult->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DraftPlanCurrencyCountryInputSearchResultViewData);

        if (empty($data->countries)) {
            return new SendMessageContext(
                chatId: $data->chatId,
                text: $this->translator->trans('trip.context.country.not_found'),
            );
        }

        $keyboard = [];
        foreach ($data->countries as $country) {
            $keyboard[] = [[
                'text' => $country->name,
                'callback_data' => CallbackQueryData::DraftPlanCurrencyCountryPick->value . $country->placeId,
            ]];
        }

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.exchange.country.choice.message'),
            replyMarkup: ['inline_keyboard' => $keyboard]
        );
    }
}
