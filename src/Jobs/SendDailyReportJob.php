<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Scheduler\Contracts\JobInterface;

final class SendDailyReportJob implements JobInterface
{
    public function run(): void
    {
        // Job logic goes here.
    }

    public function getName(): string
    {
        return 'send-daily-report';
    }

    public function getCronExpression(): string
    {
        return '0 8 * * *';
    }

    public function withoutOverlapping(): bool
    {
        return true;
    }

    public function getMutexTtl(): int
    {
        return 3600;
    }
}
