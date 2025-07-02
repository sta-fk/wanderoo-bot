<?php

namespace App\Service\TelegramViewer\PlanGenerationFinishedViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\PlanGenerationFinishedViewData\EditStopDurationRequestViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EditStopDurationRequestViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::EditStopDurationRequest);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof EditStopDurationRequestViewData);

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.edit.prompt.duration', [
                '{city}' => $data->cityName,
            ])
        );
    }
}
