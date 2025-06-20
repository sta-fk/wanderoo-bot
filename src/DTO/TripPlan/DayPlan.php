<?php

namespace App\DTO\TripPlan;

class DayPlan
{
    public ?\DateTimeImmutable $date = null;

    /** @var string[]  */
    public array $activities = [];

    /** @var string[] */
    public array $foodPlaces = [];
}
