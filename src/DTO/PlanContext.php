<?php

namespace App\DTO;

class PlanContext
{
    public ?string $continent = null;
    public ?string $country = null;
    public ?string $city = null;

    public static function fromArray(array $data): self
    {
        $ctx = new self();
        $ctx->continent = $data['continent'] ?? null;
        $ctx->country = $data['country'] ?? null;
        $ctx->city = $data['city'] ?? null;

        return $ctx;
    }

    public function toArray(): array
    {
        return [
            'continent' => $this->continent,
            'country' => $this->country,
            'city' => $this->city,
        ];
    }
}
