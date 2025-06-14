<?php

namespace App\Service\Budget;

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
        if (!array_key_exists($rangeKey, self::BUDGET_RANGES_USD)) {
            throw new \InvalidArgumentException("Unknown budget range: $rangeKey");
        }

        if ('none' === $rangeKey) {
            return null;
        }

        [$min, $max] = self::BUDGET_RANGES_USD[$rangeKey];
        $minConverted = $this->exchanger->convert($min, CallbackQueryData::Usd->value, $stopCurrency);
        $maxConverted = $max !== null
            ? $this->exchanger->convert($max, CallbackQueryData::Usd->value, $stopCurrency)
            : null;

        return [$minConverted, $maxConverted];
    }

    public function applyBudgetToStop(StopContext $stopContext, PlanContext $context, float $enteredBudgetInPlan): void
    {
        $stopContext->budgetInPlanCurrency = round($enteredBudgetInPlan, -1);

        if (empty($context->stops)) {
            $stopContext->budget = round($enteredBudgetInPlan, -1);
            return;
        }

        if ($stopContext->currency !== $context->currency) {
            $converted = $this->convertStopBudgetToPlanCurrency(
                $enteredBudgetInPlan,
                $context->currency,
                $stopContext->currency
            );
            $stopContext->budget = round($converted, -1);
        } else {
            $stopContext->budget = round($enteredBudgetInPlan, -1);
        }
    }

    public function recalculateAllStopBudgetsToNewCurrency(PlanContext $context, string $newCurrency): void
    {
        foreach ($context->stops as $stop) {
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

        $context->currency = $newCurrency;
        $context->isSetDefaultCurrency = true;
        $context->updateTotalBudget();
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
