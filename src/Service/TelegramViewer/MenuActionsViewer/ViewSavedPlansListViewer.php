<?php

namespace App\Service\TelegramViewer\MenuActionsViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\Internal\MenuActionsViewData\ViewSavedPlansListViewData;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Entity\Trip;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ViewSavedPlansListViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::ViewSavedPlansList);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ViewSavedPlansListViewData);

        if (empty($data->trips)) {
            return new SendMessageContext(
                chatId: $data->chatId,
                text: $this->translator->trans('trip.list.empty'),
            );
        }

        $keyboard = array_map(static function (Trip $trip) {
            return [[
                'text' => sprintf("➡️ %s", $trip->getTitle()),
                'callback_data' => CallbackQueryData::ViewPlanDetails->value . $trip->getId()
            ]];
        }, $data->trips);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.list.select'),
            replyMarkup: ['inline_keyboard' => $keyboard],
        );
    }
}
