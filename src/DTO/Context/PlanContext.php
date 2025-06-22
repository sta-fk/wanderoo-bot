<?php

namespace App\DTO\Context;

use App\DTO\Context\StopContext;

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

    public function getLastSavedStop(): StopContext
    {
        return $this->stops[count($this->stops) - 1] ?? throw new \RuntimeException('Unable to find the last stop context');
    }

    public function enableAddingStopFlow(): self
    {
        $this->isAddingStopFlow = true;

        return $this;
    }

    public function finishCreatingNewStop(): self
    {
        if (null === $this->currentStopDraft->countryName) {
            return $this;
        }

        $this->stops[] = $this->currentStopDraft;
        $this->resetCurrentStopDraft();
        $this->updateTotalBudget();
        $this->updateTotalDuration();
        $this->updateEndDate();
        $this->disableAddingStopFlow();

        return $this;
    }

    public function resetCurrentStopDraft(): self
    {
        $this->currentStopDraft = new StopContext();

        return $this;
    }

    public function disableAddingStopFlow(): self
    {
        $this->isAddingStopFlow = false;

        return $this;
    }

    public function updateTotalBudget(): void
    {
        $total = 0;

        foreach ($this->stops as $stop) {
            if (isset($stop->budgetInPlanCurrency)) {
                $total += $stop->budgetInPlanCurrency;
            }
        }

        $this->totalBudget = round($total, -1);
    }

    private function updateTotalDuration(): void
    {
        $total = 0;

        foreach ($this->stops as $stop) {
            if (isset($stop->duration)) {
                $total += $stop->duration;
            }
        }

        $this->totalDuration = $total;
    }

    private function updateEndDate(): void
    {
        if ($this->startDate && $this->totalDuration) {
            $this->endDate = $this->startDate->modify('+' . $this->totalDuration . ' days');
        } else {
            $this->endDate = null;
        }
    }
}
