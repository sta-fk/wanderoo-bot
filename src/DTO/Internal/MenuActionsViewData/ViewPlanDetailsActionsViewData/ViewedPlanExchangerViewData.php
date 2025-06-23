<?php

namespace App\DTO\Internal\MenuActionsViewData\ViewPlanDetailsActionsViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;
use Symfony\Component\Uid\Uuid;

class ViewedPlanExchangerViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public Uuid $tripId,
        public float $baseAmount,
        public string $baseCurrency,
        public array $convertedAmounts, // [ 'USD' => 1450.23, 'EUR' => 1320.10, ... ]
        public ?string $userDefaultCurrency,
    ) {}

    public function getCurrentView(): MessageView
    {
        return MessageView::ViewedPlanExchanger;
    }
}
