<?php

namespace App\Service;

use App\DTO\PlanContext;
use App\DTO\StopContext;
use App\Enum\CallbackQueryData;
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
    ) {
    }

    public function resolveBudgetRange(string $rangeKey, string $stopCurrency): ?array
    {
        if (!isset(self::BUDGET_RANGES_USD[$rangeKey])) {
            throw new \InvalidArgumentException("Unknown budget range: $rangeKey");
        }

        [$min, $max] = self::BUDGET_RANGES_USD[$rangeKey];
        $minConverted = $this->exchanger->convert($min, CallbackQueryData::Usd->value, $stopCurrency);
        $maxConverted = $max !== null
            ? $this->exchanger->convert($max, CallbackQueryData::Usd->value, $stopCurrency)
            : null;

        return [$minConverted, $maxConverted];
    }

    public function applyBudgetToStop(StopContext $stop, PlanContext $plan, float $enteredBudget, bool $isFirstStop = false): void
    {
        $stop->budgetInPlanCurrency = round($enteredBudget, -1);

        if ($isFirstStop) {
            $plan->currency = $stop->currency;
            $stop->budget = round($enteredBudget, -1);
            return;
        }

        if ($stop->currency !== $plan->currency) {
            $converted = $this->convertStopBudgetToPlanCurrency(
                $enteredBudget,
                $plan->currency,
                $stop->currency
            );
            $stop->budget = round($converted, -1);
        } else {
            $stop->budget = round($enteredBudget, -1);
        }

        $plan->updateTotalBudget();
    }

    public function recalculateAllStopBudgetsToNewCurrency(PlanContext $plan, string $newCurrency): void
    {
        foreach ($plan->stops as $stop) {
            if (!$stop->budget || !$stop->currency) {
                continue;
            }

            if ($stop->currency !== $newCurrency) {
                $converted = $this->convertStopBudgetToPlanCurrency(
                    $stop->budget,
                    $stop->currency,
                    $newCurrency
                );
                $stop->budgetInPlanCurrency = round($converted, -1);
            } else {
                $stop->budgetInPlanCurrency = $stop->budget;
            }
        }

        $plan->currency = $newCurrency;
        $plan->updateTotalBudget();
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
