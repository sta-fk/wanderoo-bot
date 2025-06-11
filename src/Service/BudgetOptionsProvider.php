<?php

namespace App\Service;

use App\DTO\PlanContext;
use App\DTO\StopContext;
use App\Service\Integrations\CurrencyExchangerService;

readonly class BudgetOptionsProvider
{
    public function __construct(
        private CurrencyExchangerService $exchanger
    ) {}

    public function getBudgetOptionsInCurrency(string $targetCurrency): array
    {
        $labels = [];

        foreach (BudgetHelperService::BUDGET_RANGES_USD as $key => $range) {
            if ($key === 'none') {
                $labels[$key] = 'Без бюджету';
                continue;
            }

            [$min, $max] = $range;
            $minConv = $this->exchanger->convert($min, 'USD', $targetCurrency);
            $maxConv = $max !== null
                ? $this->exchanger->convert($max, 'USD', $targetCurrency)
                : null;

            $label = match (true) {
                $key === '0_300' => "До ~{$this->round($maxConv)} $targetCurrency",
                $key === '300_700', $key === '700_1500'
                    => "~{$this->round($minConv)} — {$this->round($maxConv)} $targetCurrency",
                $key === '1500_plus' => "Понад ~{$this->round($minConv)} $targetCurrency",
                default => '???'
            };

            $labels[$key] = $label;
        }

        $labels['custom'] = 'Ввести вручну';

        return $labels;
    }

    public function round(float $amount): int
    {
        return round($amount, -1); // округлення до десятка
    }
}
