<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CountryInputSearchResultViewData;
use App\DTO\Internal\CurrencyCountryInputSearchResultViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CurrencyCountryInputSearchResultViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::CurrencyCountryInputSearchResult->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CurrencyCountryInputSearchResultViewData);

        if (empty($data->countries)) {
            return new SendMessageContext(
                $data->chatId,
                $this->translator->trans('trip.context.country.not_found'),
            );
        }

        $keyboard = [];
        foreach ($data->countries as $country) {
            $keyboard[] = [[
                'text' => $country->name,
                'callback_data' => CallbackQueryData::CurrencyCountryPick->value . $country->placeId,
            ]];
        }

        return new SendMessageContext(
            $data->chatId,
            $this->translator->trans('trip.context.currency.country.choice'),
            ['inline_keyboard' => $keyboard]
        );
    }
}
