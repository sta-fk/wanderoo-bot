<?php

namespace App\Service;

use App\DTO\PlanContext;
use App\DTO\StopContext;
use App\Service\Integrations\CurrencyExchangerService;

readonly class BudgetHelperService
{
    public const BUDGET_RANGES_USD = [
        'none' => null,
        '0_300' => [0, 300],
        '300_700' => [300, 700],
        '700_1500' => [700, 1500],
        '1500_plus' => [1500, null],
    ];

    public function __construct(
        private CurrencyExchangerService $exchanger
    ) {}

    public function resolveBudgetRange(string $rangeKey, string $targetCurrency): ?array
    {
        if (!isset(self::BUDGET_RANGES_USD[$rangeKey])) {
            throw new \InvalidArgumentException("Unknown budget range: $rangeKey");
        }

        [$min, $max] = self::BUDGET_RANGES_USD[$rangeKey];
        $minConverted = $this->exchanger->convert($min, 'USD', $targetCurrency);
        $maxConverted = $max !== null
            ? $this->exchanger->convert($max, 'USD', $targetCurrency)
            : null;

        return [$minConverted, $maxConverted];
    }

    public function applyBudgetToStop(
        StopContext $stop,
        PlanContext $plan,
        float $enteredBudget,
        string $enteredCurrency,
    ): void {
        $stop->budget = $enteredBudget;
        $stop->budgetCurrency = $enteredCurrency;

        if ($plan->currency && $enteredCurrency !== $plan->currency) {
            $converted = $this->convertStopBudgetToPlanCurrency(
                $enteredBudget,
                $enteredCurrency,
                $plan->currency
            );

            $stop->budgetInPlanCurrency = round($converted, -1);
        } else {
            $stop->budgetInPlanCurrency = $enteredBudget;
        }
    }

    public function convertStopBudgetToPlanCurrency(
        ?float $stopBudget,
        string $stopCurrency,
        string $planCurrency
    ): ?float {
        if ($stopBudget === null) {
            return null;
        }

        return $this->exchanger->convert($stopBudget, $stopCurrency, $planCurrency);
    }
}
