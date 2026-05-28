<?php

declare(strict_types=1);

namespace App\Scheduler\Laravel;

use App\Scheduler\Contracts\MutexInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final class CacheMutex implements MutexInterface
{
    private const KEY_PREFIX = 'scheduler_mutex_';

    public function __construct(private readonly CacheRepository $cache)
    {
    }

    public function acquire(string $key, int $ttl): bool
    {
        return $this->cache->add(self::KEY_PREFIX . $key, 1, $ttl);
    }

    public function release(string $key): void
    {
        $this->cache->forget(self::KEY_PREFIX . $key);
    }

    public function isLocked(string $key): bool
    {
        return $this->cache->has(self::KEY_PREFIX . $key);
    }
}
