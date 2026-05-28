<?php

declare(strict_types=1);

namespace App\Scheduler\Laravel;

use App\Scheduler\Contracts\JobInterface;
use App\Scheduler\Contracts\MutexInterface;
use App\Scheduler\Contracts\SchedulerInterface;
use Cron\CronExpression;
use Illuminate\Console\Scheduling\Schedule;

final class LaravelScheduler implements SchedulerInterface
{
    /** @var JobInterface[] */
    private array $jobs = [];

    public function __construct(
        private readonly Schedule $schedule,
        private readonly MutexInterface $mutex
    ) {
    }

    public function schedule(JobInterface $job): static
    {
        $this->jobs[] = $job;

        return $this;
    }

    public function run(?\DateTimeInterface $dateTime = null): void
    {
        $runAt = $dateTime ?? new \DateTimeImmutable();

        foreach ($this->jobs as $job) {
            if (!$this->isDue($job, $runAt)) {
                continue;
            }

            if ($job->withoutOverlapping() && !$this->mutex->acquire($job->getName(), $job->getMutexTtl())) {
                continue;
            }

            try {
                $job->run();
            } finally {
                if ($job->withoutOverlapping()) {
                    $this->mutex->release($job->getName());
                }
            }
        }
    }

    public function getJobs(): array
    {
        return $this->jobs;
    }

    private function isDue(JobInterface $job, \DateTimeInterface $dateTime): bool
    {
        $cron = new CronExpression($job->getCronExpression());

        return $cron->isDue($dateTime);
    }
}
