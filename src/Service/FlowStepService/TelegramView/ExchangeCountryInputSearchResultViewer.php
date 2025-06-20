<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CountryInputSearchResultViewData;
use App\DTO\Internal\CurrencyCountryInputSearchResultViewData;
use App\DTO\Internal\ExchangeCountryInputSearchResultViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ExchangeCountryInputSearchResultViewer implements TelegramViewerInterface
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
        assert($data instanceof ExchangeCountryInputSearchResultViewData);

        if (empty($data->countries)) {
            return new SendMessageContext(
                $data->chatId,
                $this->translator->trans('trip.context.exchange.country.not_found'),
            );
        }

        $keyboard = [];
        foreach ($data->countries as $country) {
            $keyboard[] = [[
                'text' => $country->name,
                'callback_data' => CallbackQueryData::ExchangeCountryPick->value . $country->placeId,
            ]];
        }

        return new SendMessageContext(
            $data->chatId,
            $this->translator->trans('trip.context.exchange.country.choice'),
            ['inline_keyboard' => $keyboard]
        );
    }
}
