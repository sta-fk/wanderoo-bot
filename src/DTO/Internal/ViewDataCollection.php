<?php

namespace App\DTO\Internal;

use App\Enum\States;
use Doctrine\Common\Collections\ArrayCollection;

final class ViewDataCollection
{
    private ArrayCollection $viewDataCollection;
    private ?States $nextState = null;

    public function __construct()
    {
        $this->viewDataCollection = new ArrayCollection();
    }

    public function add(ViewDataInterface $viewData): self
    {
        $this->viewDataCollection->add($viewData);
        return $this;
    }

    public function toArray(): array
    {
        return $this->viewDataCollection->toArray();
    }

    public function getNextState(): ?States
    {
        return $this->nextState;
    }

    public function setNextState(States $nextState): self
    {
        $this->nextState = $nextState;
        return $this;
    }

    public static function createWithSingleViewData(ViewDataInterface $viewData): ViewDataCollection
    {
        $viewDataCollection = new self();
        $viewDataCollection->add($viewData);
        return $viewDataCollection;
    }

    public static function createStateAwareWithSingleViewData(ViewDataInterface $viewData, States $nextState): ViewDataCollection
    {
        $viewDataCollection = new self();
        $viewDataCollection->add($viewData);
        $viewDataCollection->setNextState($nextState);
        return $viewDataCollection;
    }
}
