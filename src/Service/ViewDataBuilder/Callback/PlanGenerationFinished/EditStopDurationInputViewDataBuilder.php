<?php

namespace App\Service\ViewDataBuilder\Callback\PlanGenerationFinished;

use App\DTO\Internal\PlanGenerationFinishedViewData\EditStopDurationConfirmationViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\States;
use App\Service\UserStateStorage;
use App\Service\ViewDataBuilder\StateAwareViewDataBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class EditStopDurationInputViewDataBuilder implements StateAwareViewDataBuilderInterface
{
    public function __construct(
        private UserStateStorage $stateStorage,
        private TranslatorInterface $translator,
    ) {}

    public function supportsStates(): array
    {
        return [States::WaitingForEditingDurationInput];
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return $update->isMessageUpdate();
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $chatId = $update->getChatId();
        $context = $this->stateStorage->getContext($chatId);
        $text = trim($update->message->text);

        if (!is_numeric($text) || (int) $text <= 0) {
            return ViewDataCollection::createStateAwareWithSingleViewData(
                new EditStopDurationConfirmationViewData(
                    $chatId,
                    $this->translator->trans('trip.edit.error.invalid_duration')
                ),
                States::WaitingForEditingDurationInput
            );
        }

        $duration = (int) $text;
        $context->stops[$context->editingStopIndex]->duration = $duration;
        $this->stateStorage->saveContext($chatId, $context);

        return ViewDataCollection::createStateAwareWithSingleViewData(
            new EditStopDurationConfirmationViewData(
                $chatId,
                $this->translator->trans('trip.edit.confirm.duration_updated', ['%days%' => $duration])
            ),
            States::EditingPlanStop
        );
    }
}
