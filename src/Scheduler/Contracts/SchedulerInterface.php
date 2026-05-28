<?php

declare(strict_types=1);

namespace App\Scheduler\Contracts;

interface SchedulerInterface
{
    public function schedule(JobInterface $job): static;

    public function run(?\DateTimeInterface $dateTime = null): void;

    /**
     * @return JobInterface[]
     */
    public function getJobs(): array;
}
