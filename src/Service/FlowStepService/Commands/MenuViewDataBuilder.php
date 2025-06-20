<?php

namespace App\Service\FlowStepService\Commands;

use App\DTO\Internal\MenuViewData;
use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use App\Enum\SupportedLanguages;
use App\Enum\TelegramCommands;
use App\Service\FlowStepService\ViewDataBuilderInterface;
use App\Service\TelegramCommandMenuService;

readonly class MenuViewDataBuilder implements ViewDataBuilderInterface
{
    public function __construct(
        private TelegramCommandMenuService $commandMenuService,
    ) {
    }

    public function supportsUpdate(TelegramUpdate $update): bool
    {
        return TelegramCommands::Start->value === $update->message?->text;
    }

    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection
    {
        $this->commandMenuService->setDefaultMenuButton();
        $this->commandMenuService->setCommandsForLanguage(SupportedLanguages::fromExternalLocale($update->message->from->languageCode));

        return ViewDataCollection::createWithSingleViewData(new MenuViewData($update->message->chat->id));
    }
}
