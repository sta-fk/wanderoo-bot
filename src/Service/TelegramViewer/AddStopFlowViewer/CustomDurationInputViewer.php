<?php

namespace App\Service\TelegramViewer\AddStopFlowViewer;

use App\DTO\Internal\AddStopFlowViewData\CustomDurationInputViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CustomDurationInputViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::CustomDurationInput);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CustomDurationInputViewData);

        if (!empty($data->validationFailedMessage)) {
            return new SendMessageContext(
                chatId: $data->chatId,
                text: $data->validationFailedMessage,
            );
        }

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.context.custom_duration.input'),
        );
    }
}
