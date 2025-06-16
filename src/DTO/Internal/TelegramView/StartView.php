<?php

namespace App\DTO\Internal\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewData\StartViewData;
use App\DTO\Internal\ViewData\ViewDataInterface;
use App\DTO\TelegramMessage\SendMessageContext;
use App\Enum\CallbackData;
use App\Enum\View;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class StartView implements TelegramViewInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return View::Start->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): SendMessageContext
    {
        assert($data instanceof StartViewData);

        return new SendMessageContext(
            chatId: $data->getChatId(),
            text: $this->translator->trans('trip.context.start.message'),
            replyMarkup: [
                'inline_keyboard' => [
                    [[
                        'text' => $this->translator->trans('trip.context.start.keyboard.' . CallbackData::StartNew->value),
                        'callback_data' => CallbackData::StartNew->value
                    ]],
                    [[
                        'text' => $this->translator->trans('trip.context.start.keyboard.' . CallbackData::ViewSaved->value),
                        'callback_data' => CallbackData::ViewSaved->value
                    ]],
                ]
            ],
        );
    }
}
