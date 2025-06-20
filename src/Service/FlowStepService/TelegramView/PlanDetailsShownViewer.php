<?php

namespace App\Service\FlowStepService\TelegramView;

use App\DTO\Internal\CityInputViewData;
use App\DTO\Internal\CustomDurationInputViewData;
use App\DTO\Internal\DurationProcessedViewData;
use App\DTO\Internal\PlanGenerationFinishedViewData;
use App\DTO\Internal\PlanIsGeneratingViewData;
use App\DTO\Internal\MessageViewIdentifier;
use App\DTO\Internal\PlanDetailsShownViewData;
use App\DTO\Internal\ViewDataInterface;
use App\DTO\TelegramMessageResponse\SendMessageContext;
use App\DTO\TelegramMessageResponse\TelegramMessageInterface;
use App\Enum\CallbackQueryData;
use App\Enum\MessageView;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PlanDetailsShownViewer implements TelegramViewerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(MessageViewIdentifier $identifier): bool
    {
        return MessageView::PlanDetailsShown->value === $identifier->value;
    }

    public function render(ViewDataInterface $data): TelegramMessageInterface
    {
        assert($data instanceof PlanDetailsShownViewData);

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✏️ Змінити', 'callback_data' => CallbackQueryData::EditPlan->value . $data->requiredPlanId],
                    ['text' => '🗑️ Видалити', 'callback_data' => CallbackQueryData::DeletePlan->value . $data->requiredPlanId],
                ],
                [
                    ['text' => '⬅️ Назад', 'callback_data' => CallbackQueryData::ViewSavedPlansList->value],
                ]
            ]
        ];
        ;

        return new SendMessageContext(
            $data->chatId,
            $this->translator->trans('trip.context.to_plan.message'),
            $keyboard
        );
    }
}
