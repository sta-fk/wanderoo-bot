<?php

namespace App\DTO\Internal;

use App\Enum\MessageView;
use App\Enum\States;

final readonly class MessageViewIdentifier
{
    public function __construct(public string $value)
    {
    }

    public function equals(MessageView $view): bool
    {
        return $view->value === $this->value;
    }

    public static function fromState(States $state): self
    {
        return new self($state->value);
    }

    public static function fromView(MessageView $view): self
    {
        return new self($view->value);
    }
}
