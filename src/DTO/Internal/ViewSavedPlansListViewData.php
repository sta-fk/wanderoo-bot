<?php

namespace App\DTO\Internal;

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
