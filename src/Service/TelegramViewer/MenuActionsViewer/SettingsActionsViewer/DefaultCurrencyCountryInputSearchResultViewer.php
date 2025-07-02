<?php

namespace App\Service\TelegramViewer\MenuActionsViewer\SettingsActionsViewer;

use App\DTO\Internal\MenuActionsViewData\SettingsActionsViewData\DefaultCurrencyCountryInputSearchResultViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DefaultCurrencyCountryInputSearchResultViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::DefaultCurrencyCountryInputSearchResult);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof DefaultCurrencyCountryInputSearchResultViewData);

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
                'callback_data' => CallbackQueryData::DefaultCurrencyCountryPick->value . $country->placeId,
            ]];
        }

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.default_currency.country.choice.message'),
            replyMarkup: ['inline_keyboard' => $keyboard]
        );
    }
}
