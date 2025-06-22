<?php

namespace App\Service\FlowViewer;

interface StateAwareViewDataBuilderInterface extends ViewDataBuilderInterface
{
    public function supportsStates(): array;
}
