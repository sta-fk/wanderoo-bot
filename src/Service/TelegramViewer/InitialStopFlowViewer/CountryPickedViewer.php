<?php

namespace App\Service\TelegramViewer\InitialStopFlowViewer;

use App\DTO\Internal\InitialStopFlowViewData\CountryPickedViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CountryPickedViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return $identifier->equals(MessageView::CountryPicked);
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof CountryPickedViewData);

        return new AnswerCallbackQueryContext(
            callbackQueryId: $data->callbackQueryId,
            text: $this->translator->trans('trip.context.country.picked', ['{chosenCountryName}' => $data->chosenCountryName])
        );
    }
}
