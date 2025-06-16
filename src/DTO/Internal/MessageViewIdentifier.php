<?php

namespace App\DTO\Internal;

use App\Enum\States;
use App\Enum\MessageView;

final readonly class MessageViewIdentifier
{
    public function __construct(public string $value) {}

    public static function fromState(States $state): self
    {
        return new self($state->value);
    }

    public static function fromView(MessageViewIdentifier $view): self
    {
        return new self($view->value);
    }
}
