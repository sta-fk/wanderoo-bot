<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\AddStopViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class AddStopViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::AddStop->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof AddStopViewData);

        $keyboard = [
            [['text' => $this->translator->trans('trip.context.add_stop.keyboard.yes'), 'callback_data' => CallbackQueryData::StopCountryAnother->value]],
            [['text' => $this->translator->trans('trip.context.add_stop.keyboard.no', ['{last_one_country_name}' => $data->lastOneCountryName]), 'callback_data' => CallbackQueryData::StopCountrySame->value]],
        ];

        return new SendMessageContext(
            $data->chatId,
            $this->translator->trans("trip.context.add_stop.message", ['{last_one_country_name}' => $data->lastOneCountryName]),
            ['inline_keyboard' => $keyboard]
        );
    }
}
