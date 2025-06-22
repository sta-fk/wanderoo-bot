<?php

namespace App\Service\FlowViewer;

use App\DTO\Internal\ViewDataCollection;
use App\DTO\Request\TelegramUpdate;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('view_data_builder')]
interface ViewDataBuilderInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool;
    public function buildNextViewDataCollection(TelegramUpdate $update): ViewDataCollection;
}
