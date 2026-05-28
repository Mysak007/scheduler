<?php

declare(strict_types=1);

namespace Tests\Unit\Scheduler\Laravel;

use App\Scheduler\Contracts\JobInterface;
use App\Scheduler\Contracts\MutexInterface;
use App\Scheduler\Laravel\LaravelScheduler;
use Illuminate\Console\Scheduling\Schedule;
use Mockery;
use PHPUnit\Framework\TestCase;

final class LaravelSchedulerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testRunExecutesDueJob(): void
    {
        $schedule = Mockery::mock(Schedule::class);
        $mutex = new InMemoryMutex();
        $scheduler = new LaravelScheduler($schedule, $mutex);
        $job = new FakeJob('* * * * *');

        $scheduler->schedule($job);
        $scheduler->run(new \DateTimeImmutable('2026-05-28 10:00:00'));

        self::assertSame(1, $job->runCount);
    }

    public function testRunSkipsWhenLockNotAcquired(): void
    {
        $schedule = Mockery::mock(Schedule::class);
        $mutex = new InMemoryMutex(false);
        $scheduler = new LaravelScheduler($schedule, $mutex);
        $job = new FakeJob('* * * * *');

        $scheduler->schedule($job);
        $scheduler->run(new \DateTimeImmutable('2026-05-28 10:00:00'));

        self::assertSame(0, $job->runCount);
    }
}

final class FakeJob implements JobInterface
{
    public int $runCount = 0;

    public function __construct(private readonly string $cron)
    {
    }

    public function run(): void
    {
        ++$this->runCount;
    }

    public function getName(): string
    {
        return 'fake-job';
    }

    public function getCronExpression(): string
    {
        return $this->cron;
    }

    public function withoutOverlapping(): bool
    {
        return true;
    }

    public function getMutexTtl(): int
    {
        return 60;
    }
}

final class InMemoryMutex implements MutexInterface
{
    public function __construct(private readonly bool $canAcquire = true)
    {
    }

    public function acquire(string $key, int $ttl): bool
    {
        return $this->canAcquire;
    }

    public function release(string $key): void
    {
    }

    public function isLocked(string $key): bool
    {
        return !$this->canAcquire;
    }
}
