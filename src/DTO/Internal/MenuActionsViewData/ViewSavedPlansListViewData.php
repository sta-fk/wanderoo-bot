<?php

namespace App\DTO\Internal\MenuActionsViewData;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;

readonly class ViewSavedPlansListViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public array $trips = [],
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::ViewSavedPlansList;
    }
}
