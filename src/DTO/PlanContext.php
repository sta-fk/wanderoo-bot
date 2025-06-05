<?php

namespace App\DTO;

class PlanContext
{
    public ?string $country = null;
    public ?string $city = null;

    public static function fromArray(array $data): self
    {
        $ctx = new self();
        $ctx->country = $data['country'] ?? null;
        $ctx->city = $data['city'] ?? null;

        return $ctx;
    }

    public function toArray(): array
    {
        return [
            'country' => $this->country,
            'city' => $this->city,
        ];
    }
}
