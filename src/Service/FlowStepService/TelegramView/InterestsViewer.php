<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\InterestsViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
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
            return new SendMessageContext(
                chatId: $data->chatId,
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
            ? $this->translator->trans('trip.context.interests.message', ['{city_name}' => $data->cityName])
            : $this->translator->trans('trip.context.interests.continue');

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $text,
            replyMarkup: ['inline_keyboard' => $buttons],
        );
    }
}
