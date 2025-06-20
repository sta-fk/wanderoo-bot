<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CityInputSearchResultViewData;
use App\DTO\Internal\CountryInputSearchResultViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CityInputSearchResultViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::CityInputSearchResult->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CityInputSearchResultViewData);

        if (empty($data->cities)) {
            return new SendMessageContext(
                $data->chatId,
                $this->translator->trans('trip.context.city.not_found'),
            );
        }

        $keyboard = [];
        foreach ($data->cities as $city) {
            $keyboard[] = [[
                'text' => $city->name,
                'callback_data' => CallbackQueryData::City->value . $city->placeId,
            ]];
        }

        return new SendMessageContext(
            $data->chatId,
            $this->translator->trans('trip.context.city.message'),
            ['inline_keyboard' => $keyboard]
        );
    }
}
