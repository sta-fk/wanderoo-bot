<?php

namespace App\Service\FlowStepService;

use App\DTO\Internal\ViewDataInterface;
use App\DTO\Request\TelegramUpdate;
use App\DTO\TelegramResponseMessage\TelegramMessageCollection;
use App\DTO\TelegramResponseMessage\TelegramMessageInterface;
use App\DTO\TelegramResponseMessage\TelegramViewDataCollection;
use App\Enum\MessageView;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('flow_view_data_service')]
interface FlowViewDataServiceInterface
{
    public function supportsUpdate(TelegramUpdate $update): bool;

    public function buildNextStepViewData(TelegramUpdate $update): ViewDataInterface;
}
