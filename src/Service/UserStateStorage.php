<?php

namespace App\Service;

use App\DTO\Context\PlanContext;
use App\Enum\States;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class UserStateStorage
{
    private const STATE_TEMPLATE_KEY = 'chat_%s_state';
    private const CONTEXT_TEMPLATE_KEY = 'chat_%s_context';

    public function __construct(
        private CacheInterface $cache,
        private int $stateTtl,
        private int $contextTtl,
    ) {
    }

    public function updateState(int $chatId, States $state): void
    {
        $this->cache->delete(self::getStateKey($chatId)); // Очистити, щоб перезаписати

        $this->cache->get(
            self::getStateKey($chatId),
            function (ItemInterface $item) use ($state) {
                $item->expiresAfter($this->stateTtl);

                return $state->value;
            }
        );
    }

    public function getState(int $chatId): ?States
    {
        return States::tryFrom(
            $this->cache->get(
                self::getStateKey($chatId),
                function () {
                    return null;
                }
            )
        );
    }

    public function saveContext(int $chatId, PlanContext $context): void
    {
        $this->cache->delete(self::getContextKey($chatId));

        $this->cache->get(
            self::getContextKey($chatId),
            function (ItemInterface $item) use ($context) {
                $item->expiresAfter($this->contextTtl);

                return $context;
            }
        );
    }

    public function getContext(int $chatId): PlanContext
    {
        return $this->cache->get(self::getContextKey($chatId), fn () => new PlanContext());
    }

    public function resetState(int $chatId): void
    {
        $this->cache->delete(self::getStateKey($chatId));
    }

    public function clearContext(int $chatId): void
    {
        $this->cache->delete(self::getContextKey($chatId));
        $this->cache->delete(self::getStateKey($chatId));
    }

    private static function getStateKey(int $chatId): string
    {
        return sprintf(self::STATE_TEMPLATE_KEY, $chatId);
    }

    private static function getContextKey(int $chatId): string
    {
        return sprintf(self::CONTEXT_TEMPLATE_KEY, $chatId);
    }
}
