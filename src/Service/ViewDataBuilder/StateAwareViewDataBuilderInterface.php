<?php

namespace App\Service\ViewDataBuilder;

interface StateAwareViewDataBuilderInterface extends ViewDataBuilderInterface
{
    public function supportsStates(): array;
}
