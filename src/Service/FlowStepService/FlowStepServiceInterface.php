<?php

namespace App\Service\FlowStepService;

use App\DTO\Internal\ViewData\ViewDataInterface;
use App\DTO\Request\TelegramUpdate;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('flow_step_service')]
interface FlowStepServiceInterface
{
    public function supports(TelegramUpdate $update): bool;
    public function buildViewData(TelegramUpdate $update): ViewDataInterface;
}
