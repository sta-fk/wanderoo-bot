<?php

namespace App\Service\FlowStepService;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use App\Enum\States;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('flow_step_service')]
interface FlowStepServiceInterface
{
    public function supports(TelegramUpdate $update): bool;

    public function buildMessage(TelegramUpdate $update): SendMessageContext;

    public function getNextState(): States;
}
