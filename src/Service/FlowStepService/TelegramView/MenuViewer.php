<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\MenuViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class MenuViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::Menu->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof MenuViewData);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.menu.message'),
            replyMarkup: [
                'inline_keyboard' => [
                    [[
                        'text' => $this->translator->trans('trip.menu.keyboard.' . CallbackQueryData::StartNew->value),
                        'callback_data' => CallbackQueryData::StartNew->value
                    ]],
                    [
                        [
                            'text' => $this->translator->trans('trip.menu.keyboard.' . CallbackQueryData::ViewSavedPlansList->value),
                            'callback_data' => CallbackQueryData::ViewSavedPlansList->value
                        ],
                        [
                            'text' => $this->translator->trans('trip.menu.keyboard.' . CallbackQueryData::Settings->value . '.input'),
                            'callback_data' => CallbackQueryData::Settings->value
                        ],
                    ],
                ]
            ],
        );
    }
}
