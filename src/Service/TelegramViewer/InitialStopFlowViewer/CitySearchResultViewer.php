<?php

namespace App\Service\TelegramViewer\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\CitySearchResultViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CitySearchResultViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::CitySearchResult);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CitySearchResultViewData);

        if (empty($data->cities)) {
            return new SendMessageContext(
                chatId: $data->chatId,
                text: $this->translator->trans('trip.context.city.not_found')
            );
        }

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.city.search'),
            replyMarkup: ['inline_keyboard' => $this->getKeyboard($data)],
        );
    }

    private function getKeyboard(CitySearchResultViewData $data): array
    {
        $keyboard = [];
        foreach ($data->cities as $city) {
            $keyboard[] = [[
                'text' => $city->name,
                'callback_data' => CallbackQueryData::City->withValue($city->placeId)
            ]];
        }

        return $keyboard;
    }
}
