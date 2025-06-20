<?php

namespace App\DTO\Internal;

use App\DTO\Internal\ViewDataInterface;
use App\Enum\MessageView;
use App\Enum\States;

class InterestsViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public array $selectedInterests,
        public bool $interestsDone,
        public ?string $cityName = null,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::Interests;
    }
}
