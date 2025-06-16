<?php

namespace App\DTO\TripPlan;

use App\DTO\Context\PlanContext;
use App\DTO\TripPlan\StopPlan;

class TripPlan
{
    public ?string $name = null;
    public ?\DateTimeImmutable $startDate = null;
    public ?\DateTimeImmutable $endDate = null;
    public ?int $totalDuration = null;
    public ?float $totalBudget = null;
    public ?string $currency = null;

    /** @var StopPlan[] */
    public array $stops = [];

    public function calculateDates(PlanContext $plan): self
    {
        $currentDate = $plan->startDate;

        foreach ($this->stops as $stop) {
            $stop->startDate = $currentDate;

            // Тривалість задається у днях (наприклад, 2 → з 1 по 2 число включно)
            $stop->endDate = $currentDate->modify('+' . ($stop->duration - 1) . ' days');

            // Наступна дата починається з наступного дня після поточної зупинки
            $currentDate = $stop->endDate->modify('+1 day');
        }

        return $this;
    }
}
