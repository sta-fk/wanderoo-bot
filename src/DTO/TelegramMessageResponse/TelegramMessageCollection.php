<?php

namespace App\DTO\TelegramMessageResponse;

use App\DTO\Internal\ViewDataInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class TelegramMessageCollection
{
    private ArrayCollection $messagesCollection;

    public function add(TelegramMessageInterface $message): void
    {
        $this->messagesCollection->add($message);
    }

    public function remove(ViewDataInterface $message): void
    {
        $this->messagesCollection->removeElement($message);
    }

    public function toArray(): array
    {
        return $this->messagesCollection->toArray();
    }
}
