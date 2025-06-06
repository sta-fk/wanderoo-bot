<?php

namespace App\DTO;

class PlanContext
{
    public ?string $country = null;
    public ?string $city = null;
    public ?int $duration = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $tripStyle = null;



    public static function fromArray(array $data): self
    {
        $ctx = new self();
        $ctx->country = $data['country'] ?? null;
        $ctx->city = $data['city'] ?? null;
        $ctx->duration = $data['duration'] ?? null;
        $ctx->startDate = $data['startDate'] ?? null;
        $ctx->endDate = $data['endDate'] ?? null;
        $ctx->tripStyle = $data['tripStyle'] ?? null;

        return $ctx;
    }

    public function toArray(): array
    {
        return [
            'country' => $this->country,
            'city' => $this->city,
            'duration' => $this->duration,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'tripStyle' => $this->tripStyle,
        ];
    }
}
