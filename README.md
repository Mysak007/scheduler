# Scheduler Interfaces + Laravel Adapter

Minimal implementation based on three contracts:

- `App\Scheduler\Contracts\JobInterface`
- `App\Scheduler\Contracts\SchedulerInterface`
- `App\Scheduler\Contracts\MutexInterface`

Provided implementations:

- `App\Scheduler\Laravel\LaravelScheduler`
- `App\Scheduler\Laravel\CacheMutex`

Example job:

- `App\Jobs\SendDailyReportJob`

## Install

```bash
composer install
```

## Run Tests

```bash
vendor/bin/phpunit
```
