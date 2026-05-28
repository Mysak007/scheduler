<?php

declare(strict_types=1);

namespace App\Scheduler\Contracts;

interface MutexInterface
{
    public function acquire(string $key, int $ttl): bool;

    public function release(string $key): void;

    public function isLocked(string $key): bool;
}
