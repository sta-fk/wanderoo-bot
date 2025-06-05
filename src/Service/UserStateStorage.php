<?php

namespace App\Service;

use App\Dto\PlanContext;
use App\Enum\States;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class UserStateStorage
{
    public function __construct(
        private CacheInterface $cache,
        private int $stateTtl,
        private int $contextTtl,
    ) {
    }

    public function updateState(int $chatId, States $state, int $ttl = 3600): void
    {
        $this->cache->delete("user:$chatId:state"); // Очистити, щоб перезаписати

        $this->cache->get("user:$chatId:state", function (ItemInterface $item) use ($state) {
            $item->expiresAfter($this->stateTtl);

            return $state->value;
        });
    }

    public function getState(int $chatId): ?States
    {
        return States::tryFrom(
            $this->cache->get("user:$chatId:state", function () {
                return null;
            })
        );
    }

    public function saveContext(int $chatId, PlanContext $context): void
    {
        $this->cache->delete("user:$chatId:context");

        $this->cache->get("user:$chatId:context", function (ItemInterface $item) use ($context) {
            $item->expiresAfter($this->contextTtl);

            return $context;
        });
    }

    public function getContext(int $chatId): PlanContext
    {
        return $this->cache->get("user:$chatId:context", function () {
            return new PlanContext(); // Якщо ще немає — новий обʼєкт
        });
    }
}
