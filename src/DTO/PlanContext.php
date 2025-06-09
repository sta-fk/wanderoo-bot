<?php

namespace App\DTO;

class PlanContext
{
    public bool $isAddingStopFlow = false;
    public ?string $planName = null;

    public ?\DateTimeImmutable $startDate = null;
    public ?\DateTimeImmutable $endDate = null;
    public ?int $totalDuration = null;
    public ?string $budget = null;
    public ?string $currency = null;

    /** @var StopContext[] $stops */
    public array $stops = [];
    public ?StopContext $currentStopDraft = null;
}
