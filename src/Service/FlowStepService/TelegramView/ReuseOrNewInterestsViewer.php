<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ReuseOrNewInterestsViewData;
use App\DTO\Internal\ReuseOrNewTripStyleViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ReuseOrNewInterestsViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::ReuseOrNewInterests->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof ReuseOrNewInterestsViewData);

        $keyboard = [
            'inline_keyboard' => [[
                ['text' => '✅ Так', 'callback_data' => CallbackQueryData::Interest->value . CallbackQueryData::Reuse->value],
                ['text' => '❌ Ні', 'callback_data' => CallbackQueryData::Interest->value . CallbackQueryData::New->value],
            ]]
        ];

        $text = $this->translator->trans('trip.context.interests.reuse_or_new');
        if (!empty($data->interests)) {
            $text .= "\n" . implode(', ', $data->interests);
        }

        return new SendMessageContext(
            $data->chatId,
            $text,
            ['inline_keyboard' => $keyboard]
        );
    }
}
