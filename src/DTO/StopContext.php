<?php

namespace App\DTO;

class StopContext
{
    public ?string $country = null;
    public ?string $city = null;
    public ?int $duration = null;
    public ?string $tripStyle = null;
    public array $interests = [];
    public ?string $budget = null;
}
