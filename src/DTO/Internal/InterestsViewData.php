<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;

readonly class InterestsViewData implements ViewDataInterface
{
    public function __construct(
        public int $chatId,
        public ?int $messageId = null,
        public array $selectedInterests = [],
        public bool $interestsDone = false,
        public ?string $cityName = null,
    ) {
    }

    public function getCurrentView(): MessageView
    {
        return MessageView::Interests;
    }
}
