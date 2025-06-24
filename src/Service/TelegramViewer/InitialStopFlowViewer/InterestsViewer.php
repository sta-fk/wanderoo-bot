<?php

namespace App\Service\TelegramViewer\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\InterestsViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\DeleteMessageContext;
use App\DTO\TelegramMessageResponse\EditMessageTextContext;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class InterestsViewer implements TelegramViewerInterface
{
    public const INTERESTS = [
        'city' => '🏙️ Міста',
        'nature' => '🏞️ Природа',
        'food' => '🍽️ Їжа',
        'culture' => '🎭 Культура',
        'shopping' => '🛍️ Шопінг',
        'beach' => '🏖️ Пляжний відпочинок',
    ];

    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::Interests->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof InterestsViewData);

        if ($data->interestsDone) {
            if (empty($data->selectedInterests)) {
                return new DeleteMessageContext(
                    chatId: $data->chatId,
                    messageId: $data->messageId,
                );
            }

            return new EditMessageTextContext(
                chatId: $data->chatId,
                messageId: $data->messageId,
                text: $this->translator->trans('trip.context.interests.done', ['{interests}' => implode(', ', $data->selectedInterests)]),
            );
        }

        $buttons = [];
        foreach (self::INTERESTS as $key => $label) {
            $isSelected = in_array($key, $data->selectedInterests, true);
            $buttonText = ($isSelected ? '✅ ' : '⬜️ ') . $label;

            $buttons[][] = [
                'text' => $buttonText,
                'callback_data' => CallbackQueryData::Interest->value . $key,
            ];
        }

        $buttons[][] = [
            'text' => '✅ Готово',
            'callback_data' => CallbackQueryData::InterestsDone->value,
        ];

        $text = $data->cityName
            ? $this->translator->trans('trip.context.interests.message', ['{cityName}' => $data->cityName])
            : $this->translator->trans('trip.context.interests.continue');

        if (null === $data->messageId) {
            return new SendMessageContext(
                chatId: $data->chatId,
                text: $text,
                replyMarkup: ['inline_keyboard' => $buttons],
            );
        }

        return new EditMessageTextContext(
            chatId: $data->chatId,
            messageId: $data->messageId,
            text: $text,
            replyMarkup: ['inline_keyboard' => $buttons],
        );
    }
}
