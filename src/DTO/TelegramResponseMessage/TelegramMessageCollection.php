<?php

namespace App\DTO\TelegramResponseMessage;

use Doctrine\Common\Collections\ArrayCollection;

final class TelegramMessageCollection
{
    private ArrayCollection $messages;

    public function add(TelegramMessageInterface $message): void
    {
        $this->messages->add($message);
    }

    public function remove(TelegramMessageInterface $message): void
    {
        $this->messages->removeElement($message);
    }

    public function toArray(): array
    {
        return $this->messages->toArray();
    }
}
