<?php

namespace App\Service\TelegramViewer\PlanGenerationFinishedViewer;

use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\PlanGenerationFinishedViewData\EditPlanContextEntryPointViewData;
use App\DTO\Internal\PlanGenerationFinishedViewData\PlanSaveResultViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\AnswerCallbackQueryContext;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use App\Service\TelegramViewer\TelegramViewerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EditPlanContextEntryPointViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::EditPlanContextEntry->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof EditPlanContextEntryPointViewData);

        $keyboard = [];
        foreach ($data->stops as $index => $stop) {
            $keyboard[] = [[
                'text' => sprintf('%d. %s, %s', $index + 1, $stop->cityName, $stop->countryName),
                'callback_data' => CallbackQueryData::EditPlanStop->value . $index,
            ]];
        }

        $keyboard[] = [[
            'text' => $this->translator->trans('common.back'),
            'callback_data' => 'edit_plan_back_to_plan',
        ]];

        return new SendMessageContext(
            chatId: $data->chatId,
            text: $this->translator->trans('trip.edit.choose_stop'),
            replyMarkup: ['inline_keyboard' => $keyboard],
        );
    }
}
