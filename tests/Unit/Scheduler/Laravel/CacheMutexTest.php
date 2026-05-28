<?php

declare(strict_types=1);

namespace Tests\Unit\Scheduler\Laravel;

use App\Scheduler\Laravel\CacheMutex;
use Illuminate\Contracts\Cache\Repository;
use Mockery;
use PHPUnit\Framework\TestCase;

final class CacheMutexTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testAcquireReleaseAndLockedState(): void
    {
        $cache = Mockery::mock(Repository::class);
        $cache->shouldReceive('add')->once()->with('scheduler_mutex_job-a', 1, 120)->andReturn(true);
        $cache->shouldReceive('has')->once()->with('scheduler_mutex_job-a')->andReturn(true);
        $cache->shouldReceive('forget')->once()->with('scheduler_mutex_job-a');

        $mutex = new CacheMutex($cache);

        self::assertTrue($mutex->acquire('job-a', 120));
        self::assertTrue($mutex->isLocked('job-a'));
        $mutex->release('job-a');
    }
}
