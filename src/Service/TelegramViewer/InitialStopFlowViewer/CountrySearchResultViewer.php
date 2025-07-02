<?php

namespace App\Service\TelegramViewer\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\CountrySearchResultViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CountrySearchResultViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::CountrySearchResult);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CountrySearchResultViewData);

        if (empty($data->countries)) {
            return new SendMessageContext(
                chatId: $data->chatId,
                text: $this->translator->trans('trip.context.country.not_found'),
            );
        }

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.country.search'),
            replyMarkup: ['inline_keyboard' => $this->getKeyboard($data)],
        );
    }

    private function getKeyboard(CountrySearchResultViewData $data): array
    {
        $keyboard = [];
        foreach ($data->countries as $country) {
            $keyboard[] = [[
                'text' => $country->name,
                'callback_data' => CallbackQueryData::Country->withValue($country->placeId),
            ]];
        }

        return $keyboard;
    }
}
