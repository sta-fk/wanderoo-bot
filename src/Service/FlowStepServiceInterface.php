<?php

namespace App\Service;

use App\DTO\Request\TelegramUpdate;
use App\DTO\SendMessageContext;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('flow_step_service')]
interface FlowStepServiceInterface
{
    public function supports(TelegramUpdate $update): bool;
    public function buildNextStepMessage(TelegramUpdate $update): SendMessageContext;
}
