<?php

namespace App\DTO;

use App\Service\BudgetHelperService;

class PlanContext
{
    public bool $isAddingStopFlow = false;
    public bool $isSetDefaultCurrency = false;
    public ?string $planName = null;

    public ?\DateTimeImmutable $startDate = null;
    public ?\DateTimeImmutable $endDate = null;
    public ?int $totalDuration = null;
    public ?string $totalBudget = null;
    public ?string $currency = null;

    /** @var StopContext[] $stops */
    public array $stops = [];
    public ?StopContext $currentStopDraft = null;

    public function __construct()
    {
        $this->currentStopDraft = new StopContext();
    }

    public function enableAddingStopFlow(): self
    {
        $this->isAddingStopFlow = true;

        return $this;
    }

    public function disableAddingStopFlow(): self
    {
        $this->isAddingStopFlow = false;

        return $this;
    }

    public function resetCurrentStopDraft(): self
    {
        $this->currentStopDraft = new StopContext();

        return $this;
    }

    public function saveLastStopDraft(): self
    {
        if (null !== $this->currentStopDraft->countryName) {
            $this->stops[] = $this->currentStopDraft;
        }

        return $this;
    }
}
