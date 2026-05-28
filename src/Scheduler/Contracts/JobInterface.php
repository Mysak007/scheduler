<?php

declare(strict_types=1);

namespace App\Scheduler\Contracts;

interface JobInterface
{
    public function run(): void;

    public function getName(): string;

    public function getCronExpression(): string;

    public function withoutOverlapping(): bool;

    public function getMutexTtl(): int;
}
